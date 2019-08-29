<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyEndedException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has been already ended.");
    }

    public function userMessage(): string
    {
        return 'Game has been already ended.';
    }
}
