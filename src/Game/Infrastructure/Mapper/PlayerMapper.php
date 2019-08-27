<?php

namespace Game\Infrastructure\Mapper;

use Game\Domain\Entity\Player;
use Game\Infrastructure\Persistance\Eloquent\{
    User as EloquentUser,
    Step as EloquentStep
};

class PlayerMapper
{
    private $stepMapper;

    public function __construct(StepMapper $stepMapper)
    {
        $this->stepMapper = $stepMapper;
    }

    public function make(EloquentUser $eloquentUser): Player
    {
        $player = Player::createNew($eloquentUser->id);

        $playerReflection = new \ReflectionClass($player);

        if ($eloquentName = $eloquentUser->name) {
            $namePropertyReflection = $playerReflection->getProperty('name');
            $namePropertyReflection->setAccessible(true);
            $namePropertyReflection->setValue($player, $eloquentName);
        }

        if (count($eloquentUser->steps)) {
            $steps = $eloquentUser->steps->map(
                function (EloquentStep $eloquentStep) {
                    return $this->stepMapper->make($eloquentStep);
                }
            )->toArray();

            $stepsPropertyReflection = $playerReflection->getProperty('steps');
            $stepsPropertyReflection->setAccessible(true);
            $stepsPropertyReflection->setValue($player, $steps);
        }

        return $player;
    }
}
