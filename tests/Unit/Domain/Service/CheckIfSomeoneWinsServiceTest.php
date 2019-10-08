<?php

namespace Tests\Unit\Domain\Service;

use Game\Domain\Repository\{
    GameRepositoryInterface,
    StepRepositoryInterface,
    PlayerRepositoryInterface
};
use Game\Infrastructure\Repository\InMemory\{
    GameRepository,
    StepRepository,
    PlayerRepository
};
use Tests\TestCase;
use Game\Infrastructure\Persistance\Eloquent\{
    User as EloquentUser,
    Step as EloquentStep
};
use Game\Domain\Service\CheckIfSomeoneWinsService;

class CheckIfSomeoneWinsServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

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

    public function testCheckWinnerNoWinner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);

        $eloquentOwner = $playerRepository->generateStub();
        $eloquentGame = $gameRepository->generateStub([
            'owner_id' => $eloquentOwner->id,
            'competitor_id' => $playerRepository->generateStub()->id
        ]);

        $game = $gameRepository->findById($eloquentGame->id);

        $stepNo = 0;
        for ($coordinateX = 0; $coordinateX < 2; $coordinateX++) {
            for ($coordinateY = 0; $coordinateY < 3; $coordinateY++) {
                if ($stepNo % 2 === 0) {
                    $userId = $eloquentGame->owner_id;
                } else {
                    $userId = $eloquentGame->competitor_id;
                }

                $stepRepository->save($game, $player, [
                    'game_id' => $eloquentGame->id,
                    'user_id' => $userId,
                    'coordinate_x' => $coordinateX,
                    'coordinate_y' => $coordinateY
                ]);

                $stepNo++;
            }
        }

        $game = resolve(GameRepositoryInterface::class)->findById($eloquentGame->id);

        resolve(CheckIfSomeoneWinsService::class)->checkWinner($game);

        $this->assertNull($game->getWinner());
    }

    public function CheckWinnerOwnerIsWinner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => factory(EloquentUser::class)->make()->id
        ]);

        //owner steps
        for ($stepNo = 0; $stepNo < 3; $stepNo++) {
            factory(EloquentStep::class)->create([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->owner_id,
                'coordinate_x' => $stepNo,
                'coordinate_y' => $stepNo
            ]);
        }

        //competitor steps
        for ($stepNo = 1; $stepNo < 3; $stepNo++) {
            factory(EloquentStep::class)->create([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->competitor_id,
                'coordinate_x' => 0,
                'coordinate_y' => $stepNo
            ]);
        }

        $game = resolve(GameRepositoryInterface::class)->findById($eloquentGame->id);

        resolve(CheckIfSomeoneWinsService::class)->checkWinner($game);

        $this->assertNotNull($game->getWinner());
        $this->assertEquals($game->getOwner(), $game->getWinner());
    }

    public function CheckWinnerCompetitorIsWinner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => factory(EloquentUser::class)->make()->id
        ]);

        //competitor steps
        for ($stepNo = 0; $stepNo < 3; $stepNo++) {
            factory(EloquentStep::class)->create([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->competitor_id,
                'coordinate_x' => $stepNo,
                'coordinate_y' => $stepNo
            ]);
        }

        //owner steps
        for ($stepNo = 1; $stepNo < 3; $stepNo++) {
            factory(EloquentStep::class)->create([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->owner_id,
                'coordinate_x' => 0,
                'coordinate_y' => $stepNo
            ]);
        }

        $game = resolve(GameRepositoryInterface::class)->findById($eloquentGame->id);

        resolve(CheckIfSomeoneWinsService::class)->checkWinner($game);

        $this->assertNotNull($game->getWinner());
        $this->assertEquals($game->getCompetitor(), $game->getWinner());
    }
}
