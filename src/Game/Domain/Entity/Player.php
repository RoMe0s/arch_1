<?php

namespace Game\Domain\Entity;

use Game\Domain\Exception\{
    PlayerNameAlreadySetException,
    PlayerIsFullOfStepsException
};

final class Player
{
    private const MAX_COUNT_OF_STEPS = 5;

    private $uuid;

    private $name;

    private $steps = [];

    private function __construct(string $uuid, string $name = null, array $steps = [])
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->steps = $steps;
    }

    public static function createNew(string $uuid): Player
    {
        return new self($uuid);
    }

    public function setName(string $name): void
    {
        if ($this->name) {
            throw new PlayerNameAlreadySetException($this, $this->game);
        }

        $this->name = $name;
    }

    public function addStep(Step $step): void
    {
        if (count($this->steps) === self::MAX_COUNT_OF_STEPS) {
            throw new PlayerIsFullOfStepsException($this);
        }

        $this->steps[] = $step;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
