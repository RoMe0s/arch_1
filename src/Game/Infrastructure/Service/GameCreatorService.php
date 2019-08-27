<?php

namespace Game\Infrastructure\Service;

use Illuminate\Support\Str;
use Game\Domain\Entity\Game;
use Game\Domain\Exception\PlayerNotFoundException;
use Game\Infrastructure\Repository\{
    GameRepository,
    PlayerRepository
};

class GameCreatorService
{
    private $gameRepository;

    private $playerRepository;

    function __construct(GameRepository $gameRepository, PlayerRepository $playerRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
    }

    public function createGameForPlayer(string $playerId): Game
    {
        $player = $this->playerRepository->findById($playerId);
        if (!$player) {
            throw new PlayerNotFoundException($player);
        }

        $game = Game::createGame(Str::uuid(), $player);
        $this->gameRepository->save($game);

        return $game;
    }
}
