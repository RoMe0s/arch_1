<?php

namespace Game\Domain\Exception;

use Game\Domain\Aggregates\Game\Game;

class GameHaveNotStartedYetException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has not been started yet.");
    }
}
