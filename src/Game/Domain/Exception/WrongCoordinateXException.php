<?php

namespace Game\Domain\Exception;

class WrongCoordinateXException extends \Exception implements DomainException
{
    public function __construct(int $coordinateXValue)
    {
        parent::__construct("Value($coordinateXValue) for coordinate x is out of range.");
    }

    public function userMessage(): string
    {
        return 'Coordinate x is wrong.';
    }
}
