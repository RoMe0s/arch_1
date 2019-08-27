<?php

namespace Game\Domain\Entity;

final class Step
{
    private $uuid;

    private $x;

    private $y;

    public function __construct(string $uuid, CoordinateX $x, CoordinateY $y)
    {
        $this->uuid = $uuid;
        $this->x = $x;
        $this->y = $y;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getX(): CoordinateX
    {
        return $this->x;
    }

    public function getY(): CoordinateY
    {
        return $this->y;
    }
}
