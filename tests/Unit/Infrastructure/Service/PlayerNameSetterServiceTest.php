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
    PlayerIsNotAPlayerOfThisGameException
};
use Game\Infrastructure\DTO\SetPlayerNameDTO;
use Game\Infrastructure\Service\PlayerNameSetterService;
use Tests\TestCase;

class PlayerNameSetterServiceTest extends TestCase
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

    public function testSetPlayerNameWrongGame()
    {
        $dto = new SetPlayerNameDTO(
            'wrong-game-id',
            'any-player-id',
            'any-player-name'
        );

        $this->expectException(GameNotFoundException::class);

        resolve(PlayerNameSetterService::class)->setPlayerName($dto);
    }

    public function testSetPlayerNameWrongPlayer()
    {
        $eloquentGame = resolve(GameRepositoryInterface::class)->generateStub();
        $dto = new SetPlayerNameDTO(
            $eloquentGame->id,
            'any-player-id',
            'any-player-name'
        );

        $this->expectException(PlayerIsNotAPlayerOfThisGameException::class);

        resolve(PlayerNameSetterService::class)->setPlayerName($dto);
    }

    public function testSetPlayerName()
    {
        /** @var GameRepositoryInterface $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        $eloquentGame = $gameRepository->generateStub([
            'owner_name' => null
        ]);
        $dto = new SetPlayerNameDTO(
            $eloquentGame->id,
            $eloquentGame->owner_id,
            'new-owner-name'
        );

        resolve(PlayerNameSetterService::class)->setPlayerName($dto);

        $game = $gameRepository->findById($eloquentGame->id);

        $this->assertEquals('new-owner-name', $game->getOwner()->getName());
    }
}
