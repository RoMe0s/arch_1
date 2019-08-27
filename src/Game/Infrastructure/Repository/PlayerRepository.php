<?php

namespace Game\Infrastructure\Repository;

use Game\Domain\Entity\Player;
use Game\Domain\Repository\PlayerRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\User as EloquentUser;
use Game\Infrastructure\Mapper\PlayerMapper;

class PlayerRepository implements PlayerRepositoryInterface
{
    private $mapper;

    function __construct(PlayerMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Player
    {
        $eloquentUser = EloquentUser::find($id);
        if ($eloquentUser) {
            return $this->mapper->make($eloquentUser);
        }
        return null;
    }
}
