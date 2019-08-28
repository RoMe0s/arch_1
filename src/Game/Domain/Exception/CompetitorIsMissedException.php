<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;

class CompetitorIsMissedException extends \Exception
{
    public function __construct(Game $game)
    {
        parent::__construct("Competitor in the {$game->getId()} game is missed.");
    }
}
