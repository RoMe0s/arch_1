<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\Player;

class PlayerNameAlreadySetException extends \Exception implements DomainException
{
    public function __construct(Player $player)
    {
        parent::__construct("Player({$player->getId()}) name already set.");
    }

    public function userMessage(): string
    {
        return 'Player name already set.';
    }
}
