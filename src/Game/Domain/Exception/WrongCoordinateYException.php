<?php

namespace Game\Domain\Exception;

class WrongCoordinateYException extends \Exception
{
    public function __construct(int $coordinateYValue)
    {
        parent::__construct("Value($coordinateYValue) for coordinate y is out of range.");
    }
}
