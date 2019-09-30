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

    private $lastActed;

    private $steps = [];

    private function __construct(
        string $uuid,
        string $name = null,
        bool $lastActed = false,
        array $steps = []
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->lastActed = $lastActed;
        $this->steps = $steps;
    }

    public static function createNew(string $uuid): Player
    {
        return new self($uuid);
    }

    public function setName(string $name): void
    {
        if ($this->name) {
            throw new PlayerNameAlreadySetException($this);
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

    public function isStepExist(Step $step): bool
    {
        $coordinateX = $step->getX()->getValue();
        $coordinateY = $step->getY()->getValue();

        foreach ($this->getSteps() as $playerStep) {
            $playerStepCoordinateX = $playerStep->getX()->getValue();
            $playerStepCoordinateY = $playerStep->getY()->getValue();
            if ($playerStepCoordinateX === $coordinateX && $playerStepCoordinateY === $coordinateY) {
                return true;
            }
        }
        return false;
    }

    public function isLastActed(): bool
    {
        return $this->lastActed;
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
