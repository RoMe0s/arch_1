<?php

namespace Game\Domain\Service;

use Game\Domain\Exception\{
    UserIsNotAPlayerOfThisGameException,
    GameAlreadyEndedException
};
use Game\Domain\Aggregates\{
    Game\Game,
    Step\Step,
    User\User
};

final class MovementMakerService
{
    private $checkIfSomeoneWinsService;

    function __construct(CheckIfSomeoneWinsService $checkIfSomeoneWinsService)
    {
        $this->checkIfSomeoneWinsService = $checkIfSomeoneWinsService;
    }

    public function makeAMove(Game $game, Step $step): void
    {
        $player = $step->getUser();
        if (!$this->ableToMakeAMove($game, $player)) {
            throw new UserIsNotAPlayerOfThisGameException($game, $player);
        }

        if ($game->getEndedAt()) {
            throw new GameAlreadyEndedException($game);
        }

        if (!$game->getStartedAt()) {
            $game->startGame();
        }

        $game->addStep($step);

        $this->checkIfSomeoneWinsService->checkWinner($game);

        if (!$game->getEndedAt() && count($game->getSteps()) === Game::MAX_COUNT_OF_STEPS) {
            $game->endGame();
        }
    }

    private function ableToMakeAMove(Game $game, User $player): bool
    {
        $playerId = $player->getId();
        if ($game->getOwner()->getId() === $playerId) {
            return true;
        }
        $competitor = $game->getCompetitor();
        if ($competitor && $competitor->getId() === $playerId) {
            return true;
        }
        return false;
    }
}
