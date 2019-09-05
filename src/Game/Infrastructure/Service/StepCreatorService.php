<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerNotFoundException
};
use Game\Domain\Entity\{
    CoordinateX,
    CoordinateY,
    Step
};
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Domain\Repository\{
    GameRepositoryInterface,
    PlayerRepositoryInterface,
    StepRepositoryInterface
};
use Game\Domain\Service\MovementMakerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StepCreatorService
{
    private $gameRepository;

    private $playerRepository;

    private $stepRepository;

    private $movementMakerService;

    function __construct(
        GameRepositoryInterface $gameRepository,
        PlayerRepositoryInterface $playerRepository,
        StepRepositoryInterface $stepRepository,
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

        $this->movementMakerService->makeAMove($player, $game, $newStep);

        DB::transaction(function () use ($game, $player, $newStep) {
            $this->stepRepository->save($game, $player, $newStep);
            $this->gameRepository->save($game);
        });
    }
}
