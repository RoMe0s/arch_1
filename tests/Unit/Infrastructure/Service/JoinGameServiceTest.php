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
    PlayerNotFoundException
};
use Game\Infrastructure\DTO\SetCompetitorDTO;
use Game\Infrastructure\Service\JoinGameService;
use Tests\TestCase;

class JoinGameServiceTest extends TestCase
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

    public function testJoinGameWrongGame()
    {
        $dto = new SetCompetitorDTO(
            'wrong-game-id',
            'wrong-competitor-id'
        );

        $this->expectException(GameNotFoundException::class);

        resolve(JoinGameService::class)->joinGame($dto);
    }

    public function testJoinGameWrongPlayer()
    {
        $eloquentGame = resolve(GameRepositoryInterface::class)->generateStub();

        $dto = new SetCompetitorDTO(
            $eloquentGame->id,
            'wrong-competitor-id'
        );

        $this->expectException(PlayerNotFoundException::class);

        resolve(JoinGameService::class)->joinGame($dto);
    }

    public function testJoinGame()
    {
        /** @var GameRepositoryInterface $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        $eloquentGame = $gameRepository->generateStub();
        $eloquentCompetitor = resolve(PlayerRepositoryInterface::class)->generateStub();

        $dto = new SetCompetitorDTO(
            $eloquentGame->id,
            $eloquentCompetitor->id
        );

        resolve(JoinGameService::class)->joinGame($dto);

        $game = $gameRepository->findById($eloquentGame->id);

        $this->assertNotNull($game->getCompetitor());
        $this->assertEquals($eloquentCompetitor->id, $game->getCompetitor()->getId());
    }
}
