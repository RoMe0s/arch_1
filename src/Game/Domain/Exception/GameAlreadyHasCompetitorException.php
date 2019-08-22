<?php

namespace Game\Domain\Exception;

use Game\Domain\Aggregates\Game\Game;

class GameAlreadyHasCompetitorException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} already has competitor.");
    }
}
