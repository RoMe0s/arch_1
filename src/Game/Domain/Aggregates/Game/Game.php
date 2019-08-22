<?php

namespace Game\Domain\Aggregates\Game;

use Game\Domain\Exception\{
    GameAlreadyHasCompetitorException,
    GameIsFullOfStepsException,
    GameAlreadyStartedException,
    GameCannotBeEndedWithoutStartingException,
    GameAlreadyEndedException,
    GameHaveNotStartedYetException
};
use Game\Domain\Aggregates\{
    Step\Step,
    User\User
};

class Game
{
    public const MAX_COUNT_OF_STEPS = 9;

    private $uuid;

    private $owner;

    private $competitor;

    private $winner;

    private $startedAt;

    private $endedAt;

    private $steps = [];

    public function __construct(
        string $uuid,
        User $owner,
        User $competitor = null,
        User $winner = null,
        DateTime $startedAt = null,
        DateTime $endedAt = null,
        array $steps = []
    ) {
        $this->uuid = $uuid;
        $this->owner = $owner;
        $this->competitor = $competitor;
        $this->winner = $winner;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->steps = $steps;
    }

    private static function createGame(string $uuid, User $owner): Game
    {
        return new self($uuid, $owner);
    }

    public function setCompetitor(User $competitor): void
    {
        if ($this->competitor) {
            throw new GameAlreadyHasCompetitorException($this);
        }

        $this->competitor = $competitor;
    }

    public function startGame(): void
    {
        if ($this->startedAt) {
            throw new GameAlreadyStartedException($this);
        }

        $this->startedAt = new \DateTime();
    }

    public function endGame(): void
    {
        if (!$this->startedAt) {
            throw new GameCannotBeEndedWithoutStartingException($this);
        }

        if ($this->endedAt) {
            throw new GameAlreadyEndedException($this);
        }

        $this->endedAt = new \DateTime();
    }

    public function addStep(Step $step): void
    {
        if (!$this->startedAt) {
            throw new GameHaveNotStartedYetException($this);
        }

        if (count($this->steps) === self::MAX_COUNT_OF_STEPS) {
            throw new GameIsFullOfStepsException($this);
        }

        $this->steps[] = $step;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getCompetitor(): ?User
    {
        return $this->competitor;
    }

    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?\DateTime
    {
        return $this->endedAt;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }
}
