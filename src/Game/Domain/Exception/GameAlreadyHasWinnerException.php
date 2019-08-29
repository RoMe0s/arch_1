<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyHasWinnerException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} already has winner.");
    }

    public function userMessage(): string
    {
        return 'Game already has winner.';
    }
}
