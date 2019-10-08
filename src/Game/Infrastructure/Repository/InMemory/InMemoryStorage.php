<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Repository\GameRepositoryInterface;
use Game\Domain\Repository\PlayerRepositoryInterface;
use Game\Domain\Repository\StepRepositoryInterface;
use Illuminate\Support\Collection;

class InMemoryStorage
{
    private $collect;

    public function __construct()
    {
        $this->collect = collect([
            GameRepositoryInterface::class => collect(),
            StepRepositoryInterface::class => collect(),
            PlayerRepositoryInterface::class => collect(),
        ]);
    }

    public function set(string $repositoryType, string $key, $value): void
    {
        $this->collect->get($repositoryType)->put($key, $value);
    }

    public function get(string $repositoryType, string $key)
    {
        return $this->collect->get($repositoryType)->get($key);
    }

    public function all(string $repositoryType): Collection
    {
        return $this->collect->get($repositoryType, collect());
    }
}
