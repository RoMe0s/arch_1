<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Domain\Entity\Game;
use Game\Domain\Exception\PlayerNotFoundException;
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
use Game\Infrastructure\Service\GameCreatorService;
use Tests\TestCase;

class GameCreatorServiceTest extends TestCase
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

    public function testCreateGameForPlayerWrongPlayer()
    {
        $this->expectException(PlayerNotFoundException::class);

        resolve(GameCreatorService::class)->createGameForPlayer('wrong-player-id');
    }

    public function testCreateGameForPlayer()
    {
        $eloquentUser = resolve(PlayerRepositoryInterface::class)->generateStub();

        $game = resolve(GameCreatorService::class)->createGameForPlayer($eloquentUser->id);

        $this->assertInstanceOf(Game::class, $game);
    }
}
