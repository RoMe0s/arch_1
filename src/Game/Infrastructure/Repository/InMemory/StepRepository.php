<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Game;
use Game\Domain\Entity\Player;
use Game\Domain\Entity\Step;
use Game\Domain\Repository\StepRepositoryInterface;
use Game\Infrastructure\Mapper\PlayerMapper;
use Game\Infrastructure\Persistance\Eloquent\Step as EloquentStep;

class StepRepository implements StepRepositoryInterface
{
    private $storage;

    private $mapper;

    function __construct(InMemoryStorage $storage, PlayerMapper $mapper)
    {
        $this->storage = $storage;
        $this->mapper = $mapper;
    }

    public function save(Game $game, Player $player, Step $step): void
    {
        $eloquentStep = new EloquentStep([
            'id' => $step->getId(),
            'user_id' => $player->getId(),
            'game_id' => $game->getId(),
            'coordinate_x' => $step->getX()->getValue(),
            'coordinate_y' => $step->getY()->getValue(),
        ]);

        $this->storage->set(StepRepositoryInterface::class, $eloquentStep->id, $eloquentStep);
    }

    public function generateStub(array $attributes = [], bool $saveToStorage = true): EloquentStep
    {
        $eloquentStep = factory(EloquentStep::class)->make($attributes);

        if ($saveToStorage) {
            $this->storage->set(StepRepositoryInterface::class, $eloquentStep->id, $eloquentStep);
        }

        return $eloquentStep;
    }
}
