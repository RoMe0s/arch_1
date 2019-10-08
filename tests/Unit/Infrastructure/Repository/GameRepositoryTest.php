<?php

namespace Tests\Unit\Infrastructure\Repository;

use Tests\TestCase;
use Game\Infrastructure\Repository\Eloquent\GameRepository;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Game\Infrastructure\Mapper\GameMapper;

class GameRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFindById()
    {
        $eloquentGame = factory(EloquentGame::class)->create();

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);

        $this->assertEquals($eloquentGame->id, $game->getId());
    }

    public function testSave()
    {
        $stubEloquentGame = factory(EloquentGame::class)->make();
        $stubGame = resolve(GameMapper::class)->map($stubEloquentGame);
        $repository = resolve(GameRepository::class);

        $repository->save($stubGame);

        $game = $repository->findById($stubGame->getId());

        $this->assertNotNull($game);
        $this->assertEquals($game->getId(), $stubGame->getId());
    }
}
