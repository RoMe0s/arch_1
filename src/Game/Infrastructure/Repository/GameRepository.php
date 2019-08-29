<?php

namespace Game\Infrastructure\Repository;

use Game\Domain\Entity\Game;
use Game\Domain\Repository\GameRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\Game as EloquentGame;
use Game\Infrastructure\Mapper\GameMapper;

class GameRepository implements GameRepositoryInterface
{
    private $mapper;

    public function __construct(GameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findById(string $id): ?Game
    {
        $eloquentGame = EloquentGame::with(['owner', 'competitor', 'steps'])->find($id);
        if ($eloquentGame) {
            return $this->mapper->make($eloquentGame);
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

        EloquentGame::updateOrCreate(['id' => $game->getId()], [
            'owner_id' => $owner->getId(),
            'owner_name' => $owner->getName(),
            'competitor_id' => $competitor ? $competitor->getId() : null,
            'competitor_name' => $competitor ? $competitor->getName() : null,
            'winner_id' => $winner ? $winner->getId() : null,
            'started_at' => $startedAt,
            'ended_at' => $endedAt
        ]);
    }
}
