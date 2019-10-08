<?php

namespace Tests\Unit\Domain\Service;

use Game\Domain\Exception\{
    GameAlreadyEndedException,
    PlayerIsNotAbleToMakeAMoveException,
    StepIsNotUniqueException
};
use Game\Domain\Repository\{
    GameRepositoryInterface,
    PlayerRepositoryInterface,
    StepRepositoryInterface
};
use Game\Domain\Service\MovementMakerService;
use Game\Infrastructure\Mapper\StepMapper;
use Game\Infrastructure\Repository\InMemory\{
    GameRepository,
    InMemoryStorage,
    PlayerRepository,
    StepRepository
};
use Tests\TestCase;

class MovementMakerServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(
            InMemoryStorage::class,
            function () {
                return new InMemoryStorage();
            }
        );

        $this->app->bind(
            GameRepositoryInterface::class,
            GameRepository::class
        );

        $this->app->bind(
            StepRepositoryInterface::class,
            StepRepository::class
        );

        $this->app->bind(
            PlayerRepositoryInterface::class,
            PlayerRepository::class
        );
    }

    public function testMakeAMoveNotUniqueStep()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id
        ]);
        $notUniqueEloquentStep = $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0,
        ]);

        $game = $gameRepository->findById($eloquentGame->id);
        $notUniqueStep = $stepMapper->map($notUniqueEloquentStep);

        $this->expectException(StepIsNotUniqueException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $notUniqueStep);
    }

    public function testMakeAMoveGameAlreadyEnded()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id
        ]);
        $eloquentStubStep = $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0,
        ], false);

        $game = $gameRepository->findById($eloquentGame->id);
        $stubStep = $stepMapper->map($eloquentStubStep);

        $this->expectException(GameAlreadyEndedException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $stubStep);
    }

    public function testMakeAMovePlayerIsNotAbleToMakeAMove()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id,
            'ended_at' => null,
        ]);
        $eloquentStubStep = $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0,
        ], false);

        $game = $gameRepository->findById($eloquentGame->id);
        $stubStep = $stepMapper->map($eloquentStubStep);

        $this->expectException(PlayerIsNotAbleToMakeAMoveException::class);

        resolve(MovementMakerService::class)->makeAMove($game->getCompetitor(), $game, $stubStep);
    }

    public function testMakeAMoveOwner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id,
            'ended_at' => null,
        ]);
        $eloquentStubStep = $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0,
        ], false);

        $game = $gameRepository->findById($eloquentGame->id);
        $stubStep = $stepMapper->map($eloquentStubStep);

        resolve(MovementMakerService::class)->makeAMove($game->getOwner(), $game, $stubStep);

        $this->assertEquals(1, $game->getStepsCount());
        $this->assertEquals([$stubStep], $game->getOwner()->getSteps());
    }

    public function testMakeAMoveCompetitor()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id,
            'ended_at' => null,
        ]);
        $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->owner_id,
            'coordinate_x' => 0,
            'coordinate_y' => 0,
        ]);

        $eloquentStubStep = $stepRepository->generateStub([
            'game_id' => $eloquentGame->id,
            'user_id' => $eloquentGame->competitor_id,
            'coordinate_x' => 1,
            'coordinate_y' => 1,
        ], false);

        $game = $gameRepository->findById($eloquentGame->id);
        $stubStep = $stepMapper->map($eloquentStubStep);

        resolve(MovementMakerService::class)->makeAMove($game->getCompetitor(), $game, $stubStep);

        $this->assertEquals(2, $game->getStepsCount());
        $this->assertEquals([$stubStep], $game->getCompetitor()->getSteps());
    }
}
