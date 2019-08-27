<?php

namespace Game\Infrastructure\DTO;

class NewStepDTO
{
    private $gameId;

    private $playerId;

    private $x;

    private $y;

    public function __construct(string $gameId, string $playerId, int $x, int $y)
    {
        $this->gameId = $gameId;
        $this->playerId = $playerId;
        $this->x = $x;
        $this->y = $y;
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $thix->y;
    }
}
