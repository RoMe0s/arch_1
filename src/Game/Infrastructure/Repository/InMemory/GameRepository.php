<?php

namespace Game\Infrastructure\Repository\InMemory;

use Game\Domain\Entity\Game;
use Game\Domain\Repository\{
    GameRepositoryInterface,
    PlayerRepositoryInterface,
    StepRepositoryInterface
};
use Game\Infrastructure\Mapper\GameMapper;
use Game\Infrastructure\Persistance\Eloquent\{
    Game as EloquentGame,
    User as EloquentUser
};

class GameRepository implements GameRepositoryInterface
{
    private $storage;

    private $mapper;

    public function __construct(InMemoryStorage $storage, GameMapper $mapper)
    {
        $this->storage = $storage;
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Game
    {
        $eloquentGame = $this->storage->get(GameRepositoryInterface::class, $id);

        if ($eloquentGame) {
            $this->loadRelations($eloquentGame);

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

        $this->storage->set(GameRepositoryInterface::class, $eloquentGame->id, $eloquentGame);
    }

    public function generateStub(array $attributes = [], bool $saveToStorage = true): EloquentGame
    {
        if (!key_exists('owner_id', $attributes)) {
            $owner = factory(EloquentUser::class)->make();
            $attributes['owner_id'] =  $owner->id;

            $this->storage->set(PlayerRepositoryInterface::class, $owner->id, $owner);
        }

        $eloquentGame = factory(EloquentGame::class)->make($attributes);

        if ($saveToStorage) {
            $this->storage->set(GameRepositoryInterface::class, $eloquentGame->id, $eloquentGame);
        }

        return $eloquentGame;
    }

    private function loadRelations(EloquentGame $eloquentGame): void
    {
        $owner = $this->storage->all(PlayerRepositoryInterface::class)
            ->firstWhere('id', $eloquentGame->owner_id);

        $eloquentGame->setRelation('owner', $owner);

        if ($eloquentGame->competitor_id) {
            $competitor = $this->storage->all(PlayerRepositoryInterface::class)
                ->firstWhere('id', $eloquentGame->competitor_id);

            if ($competitor) {
                $eloquentGame->setRelation('competitor', $competitor);
            }
        }

        $steps = $this->storage->all(StepRepositoryInterface::class)
            ->where('game_id', $eloquentGame->id);

        $eloquentGame->setRelation('steps', $steps);
    }
}
