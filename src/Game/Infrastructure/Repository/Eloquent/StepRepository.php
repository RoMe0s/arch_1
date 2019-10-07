<?php

namespace Game\Infrastructure\Repository\Eloquent;

use Game\Domain\Entity\{
    Game,
    Player,
    Step
};
use Game\Domain\Repository\StepRepositoryInterface;
use Game\Infrastructure\Persistance\Eloquent\Step as EloquentStep;

class StepRepository implements StepRepositoryInterface
{
    public function save(Game $game, Player $player, Step $step): void
    {
        EloquentStep::updateOrCreate([
            'id' => $step->getId(),
            'user_id' => $player->getId(),
            'game_id' => $game->getId()
        ], [
            'coordinate_x' => $step->getX()->getValue(),
            'coordinate_y' => $step->getY()->getValue()
        ]);
    }
}
