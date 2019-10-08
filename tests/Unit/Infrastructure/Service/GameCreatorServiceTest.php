<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Domain\Entity\Game;
use Game\Domain\Exception\PlayerNotFoundException;
use Game\Infrastructure\Persistance\Eloquent\User;
use Game\Infrastructure\Service\GameCreatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameCreatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateGameForPlayerWrongPlayer()
    {
        $this->expectException(PlayerNotFoundException::class);

        resolve(GameCreatorService::class)->createGameForPlayer('wrong-player-id');
    }

    public function testCreateGameForPlayer()
    {
        $eloquentUser = factory(User::class)->create();

        $game = resolve(GameCreatorService::class)->createGameForPlayer($eloquentUser->id);

        $this->assertInstanceOf(Game::class, $game);
    }
}
