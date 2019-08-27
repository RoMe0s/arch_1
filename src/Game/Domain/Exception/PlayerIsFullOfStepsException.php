<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Player;

class PlayerIsFullOfStepsException extends \Exception
{
    public function __construct(Player $player)
    {
        parent::__construct("Player {$player->getId()} is full of steps.");
    }
}
