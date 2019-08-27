<?php

namespace Game\Infrastructure\DTO;

class SetPlayerNameDTO
{
    private $gameId;

    private $playerId;

    private $playerName;

    public function __construct(string $gameId, string $playerId, string $playerName)
    {
        $this->gameId = $gameId;
        $this->playerId = $playerId;
        $this->playerName = $playerName;
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }
}
