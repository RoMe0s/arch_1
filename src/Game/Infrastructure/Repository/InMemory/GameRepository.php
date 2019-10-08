<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Game;
use Game\Domain\Repository\GameRepositoryInterface;
use Game\Infrastructure\Mapper\GameMapper;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;

class GameRepository extends BaseRepository implements GameRepositoryInterface
{
    private $mapper;

    public function __construct(GameMapper $mapper)
    {
        parent::__construct();
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Game
    {
        $eloquentGame = $this->collection->get($id);
        if ($eloquentGame) {
            return $this->mapper->map($eloquentGame);
        }
        return null;
    }

    public function save(Game $game): void
    {
        $owner = $game->getOwner();
        $competitor = $game->getCompetitor();
        $winner = $game->getWinner();
        $startedAt = $game->getStartedAt();
        $endedAt = $game->getEndedAt();

        $eloquentGame = new EloquentGame([
            'id' => $game->getId(),
            'owner_id' => $owner->getId(),
            'owner_name' => $owner->getName(),
            'competitor_id' => $competitor ? $competitor->getId() : null,
            'competitor_name' => $competitor ? $competitor->getName() : null,
            'winner_id' => $winner ? $winner->getId() : null,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ]);

        $eloquentGame->setRelation('owner', $owner);
        if ($competitor) {
            $eloquentGame->setRelation('competitor', $competitor);
        }

        $this->collection->put($eloquentGame->id, $eloquentGame);
    }

    public function generateStub(array $attributes = []): EloquentGame
    {
        $eloquentGame = factory(EloquentGame::class)->make($attributes);

        $this->collection->put($eloquentGame->id, $eloquentGame);

        return $eloquentGame;
    }
}
