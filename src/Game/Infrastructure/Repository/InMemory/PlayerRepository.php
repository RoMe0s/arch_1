<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Player;
use Game\Domain\Repository\PlayerRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\User as EloquentUser;
use Game\Infrastructure\Mapper\PlayerMapper;

class PlayerRepository extends BaseRepository implements PlayerRepositoryInterface
{
    private $mapper;

    function __construct(PlayerMapper $mapper)
    {
        parent::__construct();
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Player
    {
        $eloquentUser = $this->collection->get($id);
        if ($eloquentUser) {
            return $this->mapper->map($eloquentUser);
        }
        return null;
    }

    public function generateStub(array $attributes = []): EloquentUser
    {
        $eloquentUser = factory(EloquentUser::class)->make($attributes);

        $this->collection->put($eloquentUser->id, $eloquentUser);

        return $eloquentUser;
    }
}
