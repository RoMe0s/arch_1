<?php

namespace Game\Domain\Repository;

use Game\Domain\Entity\Player;

interface PlayerRepositoryInterface
{
    public function findById(string $id): ?Player;
}
