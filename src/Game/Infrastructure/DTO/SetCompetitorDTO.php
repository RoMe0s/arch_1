<?php

namespace Game\Infrastructure\DTO;

class SetCompetitorDTO
{
    private $gameId;

    private $playerId;

    public function __construct(string $gameId, string $playerId)
    {
        $this->gameId = $gameId;
        $this->playerId = $playerId;
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }
}
