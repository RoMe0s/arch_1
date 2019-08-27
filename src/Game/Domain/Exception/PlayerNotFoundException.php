<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Player;

class PlayerNotFoundException extends \Exception
{
    public function __construct(Player $player)
    {
        parent::__construct("Player {$player->getId()} not found exception.");
    }
}
