<?php

namespace Tests\Unit\Infrastructure\Repository;

use Tests\TestCase;
use Game\Infrastructure\Repository\Eloquent\{
    StepRepository,
    GameRepository
};
use Game\Infrastructure\Persistance\Eloquent\{
    Step as EloquentStep,
    User as EloquentUser,
    Game as EloquentGame
};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Game\Infrastructure\Mapper\{
    StepMapper,
    PlayerMapper,
    GameMapper
};

class StepRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testSave()
    {
        $eloquentUser = factory(EloquentUser::class)->create();
        $eloquentGame = factory(EloquentGame::class)->create([
            'owner_id' => $eloquentUser->id
        ]);
        $stubEloquentStep = factory(EloquentStep::class)->make([
            'game_id' => null,
            'user_id' => null
        ]);

        $player = resolve(PlayerMapper::class)->map($eloquentUser);
        $game = resolve(GameMapper::class)->map($eloquentGame);
        $stubStep = resolve(StepMapper::class)->map($stubEloquentStep);

        $repository = resolve(StepRepository::class);

        $repository->save($game, $player, $stubStep);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);

        $this->assertTrue($game->getOwner()->isStepExist($stubStep));
    }
}
