<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyStartedException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has been already started.");
    }

    public function userMessage(): string
    {
        return 'Game has been already started.';
    }
}
