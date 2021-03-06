<?php

namespace Game\Domain\Exception;

class GameNotFoundException extends \Exception implements DomainException
{
    public function __construct(string $gameId)
    {
        parent::__construct("Game $gameId not found exception.");
    }

    public function userMessage(): string
    {
        return 'Game is not found.';
    }
}
