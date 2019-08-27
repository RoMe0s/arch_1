<?php

namespace Game\Domain\Repository;

use Game\Domain\Entity\Game;

interface GameRepositoryInterface
{
    public function findById(string $id): ?Game;

    public function save(Game $game): void;
}
