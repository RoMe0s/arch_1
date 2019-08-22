<?php

namespace Game\Domain\Repository;

use Game\Domain\Aggregates\Game\Game;

interface GameRepositoryInterface
{
    public function save(Game $game);
}