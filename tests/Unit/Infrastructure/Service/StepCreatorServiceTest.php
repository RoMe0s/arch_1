<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Domain\Repository\GameRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\User;
use Game\Domain\Exception\{GameNotFoundException, PlayerIsNotAPlayerOfThisGameException, PlayerNotFoundException};
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Infrastructure\Persistance\Eloquent\Game;
use Game\Infrastructure\Service\StepCreatorService;
use Tests\TestCase;

class StepCreatorServiceTest extends TestCase
{
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
        $eloquentGame = factory(Game::class)->create();
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
        $eloquentGame = factory(Game::class)->create();
        $eloquentUser = factory(User::class)->create();
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
        $eloquentGame = factory(Game::class)->create([
            'competitor_id' => factory(User::class)->create()->id,
            'ended_at' => null,
        ]);
        $dto = new NewStepDTO(
            $eloquentGame->id,
            $eloquentGame->owner_id,
            0,
            0
        );

        resolve(StepCreatorService::class)->createStepForGame($dto);

        $game = resolve(GameRepositoryInterface::class)->findById($eloquentGame->id);

        $this->assertEquals(1, $game->getStepsCount());
    }
}
