<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class GameAlreadyHasCompetitorException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Game {$game->getId()} already has competitor.");
    }

    public function userMessage(): string
    {
        return 'Competitor already exist.';
    }
}
