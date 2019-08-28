<?php

namespace Game\Domain\Service;

use Game\Domain\Exception\{GameAlreadyEndedException,
    PlayerIsNotAbleToMakeAMoveException
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
        if ($game->getEndedAt()) {
            throw new GameAlreadyEndedException($game);
        }

        if (!$game->playerIsAbleToMakeAMove($player)) {
            throw new PlayerIsNotAbleToMakeAMoveException($player, $game);
        }

        if (!$game->getStartedAt()) {
            $game->startGame();
        }

        $game->incrementStepsCount();
        $player->addStep($step);

        $this->checkIfSomeoneWinsService->checkWinner($game);

        if (!$game->getEndedAt() && ($game->getStepsCount() === Game::MAX_COUNT_OF_STEPS || $game->getWinner())) {
            $game->endGame();
        }
    }
}
