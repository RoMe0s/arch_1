<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class CompetitorIsMissedException extends \Exception implements DomainException
{
    public function __construct(Game $game)
    {
        parent::__construct("Competitor in the {$game->getId()} game is missed.");
    }

    public function userMessage(): string
    {
        return 'Competitor is missed.';
    }
}
