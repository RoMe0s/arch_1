<?php

namespace Game\Domain\Service;

use Game\Domain\Aggregates\Game\Game;

class CheckIfSomeoneWinsService
{
    private const NEEDED_COUNT_OF_STEPS = 5;

    public function checkWinner(Game $game): void
    {
        $steps = $game->getSteps();
        if (count($steps) >= self::NEEDED_COUNT_OF_STEPS) {
            //TODO: find winner
            // if ($winner) {
            //     $game->setWinner($winner);
            //     $game->endGame();
            // }
        }
    }
}