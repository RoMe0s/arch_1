<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyEndedException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has been already ended.");
    }
}
