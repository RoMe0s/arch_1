<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Game;
use Game\Domain\Entity\Step;

class StepIsNotUniqueException extends \Exception implements DomainException
{
    public function __construct(Game $game, Step $step)
    {
        parent::__construct("Step ({$step->getX()->getValue()}, {$step->getY()->getValue()}) is not unique for game - {$game->getId()}");
    }
}
