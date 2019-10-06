<?php

namespace Tests\Feature;

use Game\Infrastructure\Persistance\Eloquent\{
    Game,
    User
};
use Illuminate\Foundation\Testing\{
    RefreshDatabase,
    WithFaker
};
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testStartGameGuest()
    {
        $response = $this->json('POST', '/api/game/start');
        $response->assertStatus(401);
    }

    public function testStartGameWrongRequestType()
    {
        $this->createUserAndAuthenticate();

        //As the `GET` is occupied by the `show` method
        $response = $this->json('PUT', '/api/game/start');
        $response->assertStatus(405);
    }

    public function testStartGame()
    {
        $this->createUserAndAuthenticate();

        $response = $this->json('POST', '/api/game/start');
        $response->assertOk()->assertJsonStructure(['id']);
    }

    public function testShowGuest()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('GET', '/api/game/' . $game->id);
        $response->assertStatus(401);
    }

    public function testShowWrongRequestType()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('POST', '/api/game/' . $game->id);
        $response->assertStatus(405);
    }

    public function testShowWrongUser()
    {
        $game = factory(Game::class)->create();

        $this->createUserAndAuthenticate();

        $response = $this->json('GET',  '/api/game/' . $game->id);
        $response->assertStatus(400);
    }

    public function testShow()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id
        ]);

        $response = $this->json('GET', '/api/game/' . $game->id);
        $response->assertOk()->assertJsonStructure([
            'game' => [
                'owner' => ['name', 'steps'],
                'competitor',
                'winner',
                'startedAt',
                'endedAt',
                'stepsCount'
            ],
            'player' => ['name', 'steps'],
            'competitor'
        ]);
    }

    public function testJoinGuest()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('POST', '/api/game/' . $game->id . '/join');
        $response->assertStatus(401);
    }

    public function testJoinWrongRequestType()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('GET', '/api/game/' . $game->id . '/join');
        $response->assertStatus(405);
    }

    public function testJoin()
    {
        $competitor = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create();

        $this->actingAs($competitor, 'api');

        $response = $this->json('POST', '/api/game/' . $game->id . '/join');
        $response->assertOk();
    }

    public function testSetNameGuest()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('POST', '/api/game/' . $game->id . '/set-name');
        $response->assertStatus(401);
    }

    public function testSetNameWrongRequestType()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('GET', '/api/game/' . $game->id . '/set-name');
        $response->assertStatus(405);
    }

    public function testSetNameWrongUser()
    {
        $game = factory(Game::class)->create();

        $this->createUserAndAuthenticate();

        $response = $this->json('POST', '/api/game/' . $game->id . '/set-name', [
            'name' => $this->faker->name
        ]);
        $response->assertStatus(400);
    }

    public function testSetNameWrongData()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id,
            'owner_name' => null
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/set-name');
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testSetName()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id,
            'owner_name' => null
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/set-name', [
            'name' => $this->faker->name
        ]);
        $response->assertOk();
    }

    public function testSetNameSecondTime()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/set-name', [
            'name' => $this->faker->name
        ]);
        $response->assertStatus(400);
    }

    public function testMakeAMoveGuest()
    {
        $game = factory(Game::class)->create();

        $response = $this->json('POST', '/api/game/' . $game->id . '/move');
        $response->assertStatus(401);
    }

    public function testMakeAMoveWrongRequestType()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id
        ]);

        $response = $this->json('GET', '/api/game/' . $game->id . '/move');
        $response->assertStatus(405);
    }

    public function testMakeAMoveWrongData()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id,
            'ended_at' => null
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/move');
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => ['x', 'y']
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/move', [
            'x' => -1,
            'y' => 3
        ]);
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => ['x', 'y']
        ]);
    }

    public function testMakeAMove()
    {
        $owner = $this->createUserAndAuthenticate();
        $competitor = factory(User::class)->create();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id,
            'competitor_id' => $competitor,
            'ended_at' => null
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/move', [
            'x' => 0,
            'y' => 1
        ]);
        $response->assertOk();

        $this->actingAs($competitor, 'api');

        $response = $this->json('POST', '/api/game/' . $game->id . '/move', [
            'x' => 1,
            'y' => 0
        ]);
        $response->assertOk();
    }

    public function testMakeAMoveWrongSequence()
    {
        $owner = $this->createUserAndAuthenticate();
        $game = factory(Game::class)->create([
            'owner_id' => $owner->id,
            'competitor_id' => factory(User::class)->create()->id,
            'ended_at' => null
        ]);

        $response = $this->json('POST', '/api/game/' . $game->id . '/move', [
            'x' => 0,
            'y' => 1
        ]);
        $response->assertOk();

        $response = $this->json('POST', '/api/game/' . $game->id . '/move', [
            'x' => 0,
            'y' => 1
        ]);
        $response->assertStatus(400);
    }
}
