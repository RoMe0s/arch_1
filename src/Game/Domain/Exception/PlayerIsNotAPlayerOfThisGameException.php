<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\{
    Player,
    Game
};

class PlayerIsNotAPlayerOfThisGameException extends \Exception implements DomainException
{
    public function __construct(Game $game, Player $player)
    {
        parent::__construct("Player {$player->getId()} is not a player of the game - {$game->getId()}.");
    }

    public function userMessage(): string
    {
        return 'You are not a player of this game.';
    }
}
