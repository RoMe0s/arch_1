<?php

namespace Game\Domain\Exception;

class PlayerNotFoundException extends \Exception
{
    public function __construct(string $playerId)
    {
        parent::__construct("Player $playerId not found exception.");
    }
}
