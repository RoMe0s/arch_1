<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Domain\Repository\{
    GameRepositoryInterface,
    PlayerRepositoryInterface,
    StepRepositoryInterface
};
use Game\Infrastructure\Repository\InMemory\{
    GameRepository,
    InMemoryStorage,
    PlayerRepository,
    StepRepository
};
use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerIsNotAPlayerOfThisGameException,
    PlayerNotFoundException
};
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Infrastructure\Service\StepCreatorService;
use Tests\TestCase;

class StepCreatorServiceTest extends TestCase
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

    public function testCreateStepForGameWrongGame()
    {
        $dto = new NewStepDTO(
            'wrong-game-id',
            'any-player-id',
            0,
            0
        );

        $this->expectException(GameNotFoundException::class);

        resolve(StepCreatorService::class)->createStepForGame($dto);
    }

    public function testCreateStepForGameWrongNoExistingPlayer()
    {
        $eloquentGame = resolve(GameRepositoryInterface::class)->generateStub();
        $dto = new NewStepDTO(
            $eloquentGame->id,
            'wrong-player-id',
            0,
            0
        );

        $this->expectException(PlayerNotFoundException::class);

        resolve(StepCreatorService::class)->createStepForGame($dto);
    }

    public function testCreateStepForGameWrongExistingPlayer()
    {
        $eloquentGame = resolve(GameRepositoryInterface::class)->generateStub();
        $eloquentUser = resolve(PlayerRepositoryInterface::class)->generateStub();
        $dto = new NewStepDTO(
            $eloquentGame->id,
            $eloquentUser->id,
            0,
            0
        );

        $this->expectException(PlayerIsNotAPlayerOfThisGameException::class);

        resolve(StepCreatorService::class)->createStepForGame($dto);
    }

    public function testCreateStepForGame()
    {
        /** @var GameRepositoryInterface $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => resolve(PlayerRepositoryInterface::class)->generateStub()->id,
            'ended_at' => null,
        ]);
        $dto = new NewStepDTO(
            $eloquentGame->id,
            $eloquentGame->owner_id,
            0,
            0
        );

        resolve(StepCreatorService::class)->createStepForGame($dto);

        $game = $gameRepository->findById($eloquentGame->id);

        $this->assertEquals(1, $game->getStepsCount());
    }
}
