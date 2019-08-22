<?php

namespace Game\Domain\Exception;

class WrongCoordinateXException extends \Exception
{
    public function __construct(int $coordinateXValue)
    {
        parent::__construct("Value($coordinateXValue) for coordinate x is out of range.");
    }
}
