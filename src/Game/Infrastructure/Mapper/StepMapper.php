<?php

namespace Game\Infrastructure\Mapper;

use Game\Domain\Entity\{
    CoordinateX,
    CoordinateY,
    Step
};
use Game\Infrastructure\Persistance\Eloquent\Step as EloquentStep;

class StepMapper
{
    public function make(EloquentStep $eloquentStep): Step
    {
        $coordinateX = new CoordinateX($eloquentStep->x);
        $coordinateY = new CoordinateY($eloquentStep->y);

        return new Step($eloquentStep->id, $coordinateX, $coordinateY);
    }
}
