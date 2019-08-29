<?php

namespace Game\Domain\Exception;

class PlayerNotFoundException extends \Exception implements DomainException
{
    public function __construct(string $playerId)
    {
        parent::__construct("Player $playerId not found exception.");
    }
}
