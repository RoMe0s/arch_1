<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Game;
use Game\Domain\Entity\Player;
use Game\Domain\Entity\Step;
use Game\Domain\Repository\StepRepositoryInterface;
use Game\Infrastructure\Mapper\PlayerMapper;
use Game\Infrastructure\Persistance\Eloquent\Step as EloquentStep;

class StepRepository extends BaseRepository implements StepRepositoryInterface
{
    private $mapper;

    function __construct(PlayerMapper $mapper)
    {
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

        $this->collection->put($eloquentStep->id, $eloquentStep);
    }

    public function generateStub(array $attributes = []): EloquentStep
    {
        $eloquentStep = factory(EloquentStep::class)->make($attributes);

        $this->collection->put($eloquentStep->id, $eloquentStep);

        return $eloquentStep;
    }
}
