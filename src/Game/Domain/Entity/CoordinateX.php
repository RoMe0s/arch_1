<?php

namespace Game\Domain\Entity;

use Game\Domain\Exception\WrongCoordinateXException;

final class CoordinateX
{
    private const MIN_VALUE = 1;

    private const MAX_VALUE = 3;

    private $value;

    public function __construct(int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new WrongCoordinateXException($value);
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
