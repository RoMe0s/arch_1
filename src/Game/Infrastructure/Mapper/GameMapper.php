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

    public function map(EloquentGame $eloquentGame): Game
    {
        $lastEloquentStep = $eloquentGame->steps->last();

        $eloquentOwner = $eloquentGame->owner;
        $eloquentSteps = $eloquentGame->steps->filter(
            function (EloquentStep $eloquentStep) use ($eloquentOwner) {
                return $eloquentStep->user_id === $eloquentOwner->id;
            }
        );
        $eloquentOwner->setAttribute('steps', $eloquentSteps);
        if ($eloquentGame->owner_name) {
            $eloquentOwner->setAttribute('in_game_name', $eloquentGame->owner_name);
        }
        if ($lastEloquentStep && $lastEloquentStep->user_id === $eloquentOwner->id) {
            $eloquentOwner->setAttribute('last_acted', true);
        }
        $owner = $this->playerMapper->map($eloquentOwner);

        $game = Game::createGame($eloquentGame->id, $owner);

        $gameReflection = new \ReflectionClass(Game::class);

        if ($eloquentCompetitor = $eloquentGame->competitor) {
            $eloquentSteps = $eloquentGame->steps->filter(
                function (EloquentStep $eloquentStep) use ($eloquentCompetitor) {
                    return $eloquentStep->user_id === $eloquentCompetitor->id;
                }
            );
            $eloquentCompetitor->setAttribute('steps', $eloquentSteps);
            if ($eloquentGame->competitor_name) {
                $eloquentCompetitor->setAttribute('in_game_name', $eloquentGame->competitor_name);
            }
            if ($lastEloquentStep && $lastEloquentStep->user_id === $eloquentCompetitor->id) {
                $eloquentCompetitor->setAttribute('last_acted', true);
            }
            $competitor = $this->playerMapper->map($eloquentCompetitor);

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
