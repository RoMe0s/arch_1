<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameCannotBeEndedWithoutStartingException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} cannot be ended without starting.");
    }

    public function userMessage(): string
    {
        return 'Game cannot be ended now.';
    }
}
