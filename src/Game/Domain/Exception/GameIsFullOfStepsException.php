<?php

namespace Game\Domain\Exception;

use Game\Domain\Aggregates\Game\Game;

class GameIsFullOfStepsException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has all of available steps.");
    }
}
