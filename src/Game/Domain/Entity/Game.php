<?php

namespace Game\Domain\Entity;

use Game\Domain\Exception\{
    CompetitorAndOwnerCannotBeEqualException,
    GameAlreadyHasCompetitorException,
    GameIsFullOfStepsException,
    GameAlreadyStartedException,
    GameCannotBeEndedWithoutStartingException,
    GameAlreadyEndedException,
    GameHasNotStartedYetException,
    GameAlreadyHasWinnerException,
    PlayerIsNotAPlayerOfThisGameException,
    GameHasNotEndedYetException
};

final class Game
{
    public const MAX_COUNT_OF_STEPS = 9;

    private $uuid;

    private $owner;

    private $competitor;

    private $winnerId;

    private $startedAt;

    private $endedAt;

    private $stepsCount = 0;

    private function __construct(
        string $uuid,
        Player $owner,
        Player $competitor = null,
        int $winnerId = null,
        \DateTime $startedAt = null,
        \DateTime $endedAt = null,
        int $stepsCount = 0
    ) {
        $this->uuid = $uuid;
        $this->owner = $owner;
        $this->competitor = $competitor;
        $this->winnerId = $winnerId;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->stepsCount = $stepsCount;
    }

    public static function createGame(string $uuid, Player $owner): Game
    {
        return new self($uuid, $owner);
    }

    public function setCompetitor(Player $competitor): void
    {
        if ($this->competitor) {
            throw new GameAlreadyHasCompetitorException($this);
        }

        if ($this->owner->getId() === $this->competitor->getId()) {
            throw new CompetitorAndOwnerCannotBeEqualException($this);
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

    public function incrementStepsCount(): void
    {
        if (!$this->startedAt) {
            throw new GameHasNotStartedYetException($this);
        }

        if ($this->stepsCount === self::MAX_COUNT_OF_STEPS) {
            throw new GameIsFullOfStepsException($this);
        }

        $this->stepsCount++;
    }

    public function setWinner(Player $player): void
    {
        if (!$this->competitor || !$this->endedAt) {
            throw new GameHasNotEndedYetException($this);
        }

        if (!$this->playerIsParticipant($player)) {
            throw new PlayerIsNotAPlayerOfThisGameException($this, $player);
        }

        if ($this->winner) {
            throw new GameAlreadyHasWinnerException($this);
        }

        $this->winnerId = $player->getId();
    }

    public function playerIsTheOwner(Player $player): bool
    {
        return $this->owner->getId() === $player->getId();
    }

    public function playerIsParticipant(Player $player): bool
    {
        $playerId = $player->getId();
        if ($this->getOwner()->getId() === $playerId) {
            return true;
        }

        $competitor = $this->getCompetitor();
        return $competitor && $competitor->getId() === $playerId;
    }

    public function getPlayerOfGame(Player $player): ?Player
    {
        if (!$this->playerIsParticipant()) {
            throw new PlayerIsNotAPlayerOfThisGameException($this, $player);
        }

        if ($this->playerIsTheOwner($player)) {
            return $this->owner;
        }

        return $this->competitor;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getOwner(): Player
    {
        return $this->owner;
    }

    public function getCompetitor(): ?Player
    {
        return $this->competitor;
    }

    public function getWinner(): ?Player
    {
        $owner = $this->getOwner();
        if ($this->winnerId === $owner->getId()) {
            return $owner;
        }

        return $this->getCompetitor();
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?\DateTime
    {
        return $this->endedAt;
    }

    public function getStepsCount(): int
    {
        return $this->stepsCount;
    }
}
