<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Infrastructure\Repository\Eloquent\GameRepository;
use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerIsNotAPlayerOfThisGameException
};
use Game\Infrastructure\DTO\SetPlayerNameDTO;
use Game\Infrastructure\Persistance\Eloquent\Game;
use Game\Infrastructure\Service\PlayerNameSetterService;
use Tests\TestCase;

class PlayerNameSetterServiceTest extends TestCase
{
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
        $eloquentGame = factory(Game::class)->create();
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
        $eloquentGame = factory(Game::class)->create([
            'owner_name' => null
        ]);
        $dto = new SetPlayerNameDTO(
            $eloquentGame->id,
            $eloquentGame->owner_id,
            'new-owner-name'
        );

        resolve(PlayerNameSetterService::class)->setPlayerName($dto);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);

        $this->assertEquals('new-owner-name', $game->getOwner()->getName());
    }
}
