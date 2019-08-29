<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class CompetitorAndOwnerCannotBeEqualException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Competitor and owner cannot be equal. Game - {$game->getId()}.");
    }

    public function userMessage(): string
    {
        return 'Owner cannot be the same as competitor.';
    }
}
