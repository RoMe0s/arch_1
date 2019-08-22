<?php

namespace Game\Domain\Exception;

use Game\Domain\Aggregates\{
    Game\Game,
    User\User
};

class UserIsNotAPlayerOfThisGameException extends \Exception
{
    public function __construct(Game $game, User $user)
    {
        parent::__construct("User {$user->getId()} is not a player of the game - {$game->getId()}.");
    }
}
