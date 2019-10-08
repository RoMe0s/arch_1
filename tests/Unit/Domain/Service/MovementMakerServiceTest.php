<?php

namespace Tests\Unit\Domain\Service;

use Game\Domain\Exception\GameAlreadyEndedException;
use Game\Domain\Exception\PlayerIsNotAbleToMakeAMoveException;
use Game\Domain\Exception\StepIsNotUniqueException;
use Game\Domain\Service\MovementMakerService;
use Game\Infrastructure\Mapper\StepMapper;
use Game\Infrastructure\Persistance\Eloquent\{
    Game as EloquentGame,
    Step as EloquentStep,
    User as EloquentUser
};
use Game\Infrastructure\Repository\Eloquent\GameRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementMakerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testMakeAMoveNotUniqueStep()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id
        ]);
        $notUniqueEloquentStep = factory(EloquentStep::class)->create([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0
        ]);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);
        $notUniqueStep = resolve(StepMapper::class)->map($notUniqueEloquentStep);

        $this->expectException(StepIsNotUniqueException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $notUniqueStep);
    }

    public function testMakeAMoveGameAlreadyEnded()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id,
        ]);
        $eloquentStubStep = factory(EloquentStep::class)->make([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0
        ]);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);
        $stubStep = resolve(StepMapper::class)->map($eloquentStubStep);

        $this->expectException(GameAlreadyEndedException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $stubStep);
    }

    public function testMakeAMovePlayerIsNotAbleToMakeAMove()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id,
            'ended_at' => null
        ]);
        $eloquentStubStep = factory(EloquentStep::class)->make([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0
        ]);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);
        $stubStep = resolve(StepMapper::class)->map($eloquentStubStep);

        $this->expectException(PlayerIsNotAbleToMakeAMoveException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getCompetitor(), $game, $stubStep);
    }

    public function testMakeAMoveOwner()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id,
            'ended_at' => null
        ]);
        $eloquentStubStep = factory(EloquentStep::class)->make([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0
        ]);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);
        $stubStep = resolve(StepMapper::class)->map($eloquentStubStep);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $stubStep);

        $this->assertEquals(1, $game->getStepsCount());
        $this->assertEquals([$stubStep], $game->getOwner()->getSteps());
    }

    public function testMakeAMoveCompetitor()
    {
        $eloquentGame = factory(EloquentGame::class)->create([
            'competitor_id' => factory(EloquentUser::class)->create()->id,
            'ended_at' => null
        ]);
        factory(EloquentStep::class)->create([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0
        ]);

        $eloquentStubStep = factory(EloquentStep::class)->make([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->competitor_id,
            'coordinate_x' => 1,
            'coordinate_y' => 1
        ]);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);
        $stubStep = resolve(StepMapper::class)->map($eloquentStubStep);

        resolve(MovementMakerService::class)->makeAMove($game->getCompetitor(), $game, $stubStep);

        $this->assertEquals(2, $game->getStepsCount());
        $this->assertEquals([$stubStep], $game->getCompetitor()->getSteps());
    }
}
