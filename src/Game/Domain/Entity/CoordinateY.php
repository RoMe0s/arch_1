<?php

namespace Game\Domain\Entity;

use Game\Domain\Exception\WrongCoordinateYException;

final class CoordinateY
{
    private const MIN_VALUE = 0;

    private const MAX_VALUE = 2;

    private $value;

    public function __construct(int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new WrongCoordinateYException($value);
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
