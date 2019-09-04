<?php

namespace Game\Infrastructure\Service;

use Illuminate\Support\Str;
use Game\Domain\Entity\Game;
use Game\Domain\Exception\PlayerNotFoundException;
use Game\Domain\Repository\{
    GameRepositoryInterface,
    PlayerRepositoryInterface
};

class GameCreatorService
{
    private $gameRepository;

    private $playerRepository;

    function __construct(
        GameRepositoryInterface $gameRepository,
        PlayerRepositoryInterface $playerRepository
    ) {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
    }

    public function createGameForPlayer(string $playerId): Game
    {
        $player = $this->playerRepository->findById($playerId);
        if (!$player) {
            throw new PlayerNotFoundException($playerId);
        }

        $game = Game::createGame(Str::uuid(), $player);
        $this->gameRepository->save($game);

        return $game;
    }
}
