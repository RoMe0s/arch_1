<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameCannotBeEndedWithoutStartingException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} cannot be ended without starting.");
    }
}
