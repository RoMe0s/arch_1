<?php

namespace Game\Infrastructure\Mapper;

use Game\Domain\Entity\Game;
use Game\Infrastructure\Persistance\Eloquent\{
    Game as EloquentGame,
    Step as EloquentStep
};

class GameMapper
{
    private $playerMapper;

    public function __construct(PlayerMapper $playerMapper)
    {
        $this->playerMapper = $playerMapper;
    }

    public function make(EloquentGame $eloquentGame): Game
    {
        $eloquentOwner = $eloquentGame->owner;
        $eloquentSteps = $eloquentGame->steps->map(
            function (EloquentStep $eloquentStep) use ($eloquentOwner) {
                return $eloquentStep->user_id === $eloquentOwner->id;
            }
        );
        $eloquentOwner->setAttribute('steps', $eloquentSteps);
        if ($eloquentGame->owner_name) {
            $eloquentOwner->setAttribute('name', $eloquentGame->owner_name);
        }
        $owner = $this->playerMapper->make($eloquentOwner);

        $game = Game::createGame($eloquentGame->id, $owner);

        $gameReflection = new \ReflectionClass($game);

        if ($eloquentCompetitor = $eloquentGame->competitor) {
            $eloquentSteps = $eloquentGame->steps->map(
                function (EloquentStep $eloquentStep) use ($eloquentCompetitor) {
                    return $eloquentStep->user_id === $eloquentCompetitor->id;
                }
            );
            $eloquentCompetitor->setAttribute('steps', $eloquentSteps);
            if ($eloquentGame->competitor_name) {
                $eloquentCompetitor->setAttribute('name', $eloquentGame->competitor_name);
            }
            $competitor = $this->playerMapper->make($eloquentCompetitor);

            $competitorPropertyReflection = $gameReflection->getProperty('competitor');
            $competitorPropertyReflection->setAccessible(true);
            $competitorPropertyReflection->setValue($game, $competitor);
        }

        if ($eloquentGame->winner_id) {
            $winnerIdPropertyReflection = $gameReflection->getProperty('winnerId');
            $winnerIdPropertyReflection->setAccessible(true);
            $winnerIdPropertyReflection->setValue($game, $eloquentGame->winner_id);
        }

        if ($eloquentGame->started_at) {
            $startedAtPropertyReflection = $gameReflection->getProperty('startedAt');
            $startedAtPropertyReflection->setAccessible(true);
            $startedAtPropertyReflection->setValue($game, $eloquentGame->started_at);
        }

        if ($eloquentGame->ended_at) {
            $endedAtPropertyReflection = $gameReflection->getProperty('endedAt');
            $endedAtPropertyReflection->setAccessible(true);
            $endedAtPropertyReflection->setValue($game, $eloquentGame->ended_at);
        }

        if ($stepsCount = count($eloquentGame->steps)) {
            $stepsCountPropertyReflection = $gameReflection->getProperty('stepsCount');
            $stepsCountPropertyReflection->setAccessible(true);
            $stepsCountPropertyReflection->setValue($game, count($eloquentGame->steps));
        }

        return $game;
    }
}
