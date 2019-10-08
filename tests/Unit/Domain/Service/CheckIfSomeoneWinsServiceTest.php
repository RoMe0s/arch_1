<?php

namespace Tests\Unit\Domain\Service;

use Game\Infrastructure\Mapper\StepMapper;
use Game\Domain\Repository\{
    GameRepositoryInterface,
    StepRepositoryInterface,
    PlayerRepositoryInterface
};
use Game\Infrastructure\Repository\InMemory\{
    GameRepository,
    InMemoryStorage,
    StepRepository,
    PlayerRepository
};
use Game\Domain\Service\CheckIfSomeoneWinsService;
use Tests\TestCase;

class CheckIfSomeoneWinsServiceTest extends TestCase
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

    public function testCheckWinnerNoWinner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);
        /** @var StepMapper $stepMapper */
        $stepMapper = resolve(StepMapper::class);

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

                $player = $playerRepository->findById($userId);

                $stubStep = $stepMapper->map(
                    $stepRepository->generateStub([
                        'user_id' => $userId,
                        'game_id' => $eloquentGame->id,
                        'coordinate_x' => $coordinateX,
                        'coordinate_y' => $coordinateY
                    ])
                );

                $stepRepository->save($game, $player, $stubStep);

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
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id
        ]);

        //owner steps
        for ($stepNo = 0; $stepNo < 3; $stepNo++) {
            $stepRepository->generateStub([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->owner_id,
                'coordinate_x' => $stepNo,
                'coordinate_y' => $stepNo,
            ]);
        }

        //competitor steps
        for ($stepNo = 1; $stepNo < 3; $stepNo++) {
            $stepRepository->generateStub([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->competitor_id,
                'coordinate_x' => 0,
                'coordinate_y' => $stepNo,
            ]);
        }

        $game = $gameRepository->findById($eloquentGame->id);

        resolve(CheckIfSomeoneWinsService::class)->checkWinner($game);

        $this->assertNotNull($game->getWinner());
        $this->assertEquals($game->getOwner(), $game->getWinner());
    }

    public function CheckWinnerCompetitorIsWinner()
    {
        /** @var GameRepository $gameRepository */
        $gameRepository = resolve(GameRepositoryInterface::class);
        /** @var PlayerRepository $playerRepository */
        $playerRepository = resolve(PlayerRepositoryInterface::class);
        /** @var StepRepository $stepRepository */
        $stepRepository = resolve(StepRepositoryInterface::class);

        $eloquentGame = $gameRepository->generateStub([
            'competitor_id' => $playerRepository->generateStub()->id
        ]);

        //competitor steps
        for ($stepNo = 0; $stepNo < 3; $stepNo++) {
            $stepRepository->generateStub([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->competitor_id,
                'coordinate_x' => $stepNo,
                'coordinate_y' => $stepNo,
            ]);
        }

        //owner steps
        for ($stepNo = 1; $stepNo < 3; $stepNo++) {
            $stepRepository->generateStub([
                'game_id' => $eloquentGame->id,
                'user_id' => $eloquentGame->owner_id,
                'coordinate_x' => 0,
                'coordinate_y' => $stepNo,
            ]);
        }

        $game = $gameRepository->findById($eloquentGame->id);

        resolve(CheckIfSomeoneWinsService::class)->checkWinner($game);

        $this->assertNotNull($game->getWinner());
        $this->assertEquals($game->getCompetitor(), $game->getWinner());
    }
}
