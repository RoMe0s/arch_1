<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyHasWinnerException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} already has winner.");
    }
}
