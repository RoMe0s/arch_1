<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Player;
use Game\Domain\Repository\PlayerRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\User as EloquentUser;
use Game\Infrastructure\Mapper\PlayerMapper;

class PlayerRepository implements PlayerRepositoryInterface
{
    private $storage;

    private $mapper;

    function __construct(InMemoryStorage $storage, PlayerMapper $mapper)
    {
        $this->storage = $storage;
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Player
    {
        $eloquentUser = $this->storage->get(PlayerRepositoryInterface::class, $id);
        if ($eloquentUser) {
            return $this->mapper->map($eloquentUser);
        }
        return null;
    }

    public function generateStub(array $attributes = [], bool $saveToStorage = true): EloquentUser
    {
        $eloquentUser = factory(EloquentUser::class)->make($attributes);

        if ($saveToStorage) {
            $this->storage->set(PlayerRepositoryInterface::class, $eloquentUser->id, $eloquentUser);
        }

        return $eloquentUser;
    }
}
