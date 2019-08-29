<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameHasNotStartedYetException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} has not been started yet.");
    }

    public function userMessage(): string
    {
        return 'Game has not been started yet.';
    }
}
