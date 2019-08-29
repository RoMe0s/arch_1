<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\{
    Player,
    Game
};

class PlayerNameAlreadySetException extends \Exception implements DomainException
{
    public function __construct(Player $player, Game $game)
    {
        parent::__construct("Player({$player->getId()}) name already set for the game - {$game->getId()}.");
    }

    public function userMessage(): string
    {
        return 'Player name already set.';
    }
}
