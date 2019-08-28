<?php

namespace Game\Domain\Exception;

use Game\Domain\Entity\{
    Game,
    Player
};

class PlayerIsNotAbleToMakeAMoveException extends \Exception
{
    public function __construct(Player $player, Game $game)
    {
        parent::__construct("Player {$player->getId()} is not able to make a move in the game - {$game->getId()}.");
    }
}
