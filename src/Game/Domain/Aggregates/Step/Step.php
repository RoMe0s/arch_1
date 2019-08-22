<?php

namespace Game\Domain\Aggregates\Step;

use Game\Domain\Aggregates\User\User;

class Step
{
    private $uuid;

    private $user;

    private $x;

    private $y;

    public function __construct(string $uuid, User $user, CoordinateX $x, CoordinateY $y)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->x = $x;
        $this->y = $y;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getUser(): User
    {
        return $this->user;
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
