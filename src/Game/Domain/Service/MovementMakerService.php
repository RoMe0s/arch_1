<?php

namespace Game\Domain\Service;

use Game\Domain\Exception\{
    PlayerIsNotAPlayerOfThisGameException,
    GameAlreadyEndedException
};
use Game\Domain\Entity\{
    Player,
    Game,
    Step
};

final class MovementMakerService
{
    private $checkIfSomeoneWinsService;

    function __construct(CheckIfSomeoneWinsService $checkIfSomeoneWinsService)
    {
        $this->checkIfSomeoneWinsService = $checkIfSomeoneWinsService;
    }

    public function makeAMove(Player $player, Game $game, Step $step): void
    {
        if (!$game->playerIsParticipant($player)) {
            throw new PlayerIsNotAPlayerOfThisGameException($game, $player);
        }

        if ($game->getEndedAt()) {
            throw new GameAlreadyEndedException($game);
        }

        if (!$game->getStartedAt()) {
            $game->startGame();
        }

        $game->incrementStepsCount();
        $player->addStep($step);

        $this->checkIfSomeoneWinsService->checkWinner($game);

        if (!$game->getEndedAt() && count($game->getSteps()) === Game::MAX_COUNT_OF_STEPS) {
            $game->endGame();
        }
    }
}
