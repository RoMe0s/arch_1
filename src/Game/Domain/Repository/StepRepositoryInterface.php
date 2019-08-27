<?php

namespace Game\Domain\Repository;

use Game\Domain\Entity\{
    Game,
    Player,
    Step
};

interface StepRepositoryInterface
{
    public function save(Game $game, Player $player, Step $step): void;
}
