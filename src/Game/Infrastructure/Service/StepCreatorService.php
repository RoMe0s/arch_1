<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerNotFoundException,
    StepIsNotUniqueException
};
use Illuminate\Support\Facades\DB;
use Game\Domain\Entity\{
    Step,
    CoordinateX,
    CoordinateY
};
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Infrastructure\Repository\{
    GameRepository,
    PlayerRepository,
    StepRepository
};
use Game\Domain\Service\MovementMakerService;
use Illuminate\Support\Str;

class StepCreatorService
{
    private $gameRepository;

    private $playerRepository;

    private $stepRepository;

    private $movementMakerService;

    function __construct(
        GameRepository $gameRepository,
        PlayerRepository $playerRepository,
        StepRepository $stepRepository,
        MovementMakerService $movementMakerService
    ) {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
        $this->stepRepository = $stepRepository;
        $this->movementMakerService = $movementMakerService;
    }

    public function createStepForGame(NewStepDTO $newStepDTO): void
    {
        $gameId = $newStepDTO->getGameId();
        $game = $this->gameRepository->findById($gameId);
        if (!$game) {
            throw new GameNotFoundException($gameId);
        }

        $playerId = $newStepDTO->getPlayerId();
        $somePlayer = $this->playerRepository->findById($playerId);
        if (!$somePlayer) {
            throw new PlayerNotFoundException($playerId);
        }
        $player = $game->getPlayerOfGame($somePlayer);

        $coordinateX = new CoordinateX($newStepDTO->getX());
        $coordinateY = new CoordinateY($newStepDTO->getY());

        $newStep = new Step(Str::uuid(), $coordinateX, $coordinateY);
        if (!$game->isStepUnique($newStep)) {
            throw new StepIsNotUniqueException($game, $newStep);
        }

        $this->movementMakerService->makeAMove($player, $game, $newStep);

        DB::transaction(function () use ($game, $player, $newStep) {
            $this->stepRepository->save($game, $player, $newStep);
            $this->gameRepository->save($game);
        });
    }
}
