<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Entity\{
    Step,
    CoordinateX,
    CoordinateY,
    Player
};
use Game\Infrastructure\DTO\NewStepDTO;
use Game\Infrastructure\Repository\{
    GameRepository,
    StepRepository
};
use Game\Domain\Service\MovementMakerService;
use Illuminate\Support\Str;

class StepCreatorService
{
    private $gameRepository;

    private $stepRepository;

    private $movementMakerService;

    function __construct(
        GameRepository $gameRepository,
        StepRepository $stepRepository,
        MovementMakerService $movementMakerService
    ) {
        $this->gameRepository = $gameRepository;
        $this->stepRepository = $stepRepository;
        $this->movementMakerService = $movementMakerService;
    }

    public function createStepForGame(NewStepDTO $newStepDTO): void
    {
        $game = $this->gameRepository->findById($newStepDTO->getGameId());
        $somePlayer = Player::createNew($newStepDTO->getPlayerId());
        $player = $game->getPlayerOfGame($somePlayer);

        $coordinateX = new CoordinateX($newStepDTO->getX());
        $coordinateY = new CoordinateY($newStepDTO->getY());

        $newStep = new Step(Str::uuid(), $coordinateX, $coordinateY);
        $this->movementMakerService->makeAMove($player, $game, $newStep);

        $this->stepRepository->save($game, $player, $newStep);
    }
}
