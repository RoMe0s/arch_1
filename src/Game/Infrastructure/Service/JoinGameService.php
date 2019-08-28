<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerNotFoundException
};
use Game\Infrastructure\DTO\SetCompetitorDTO;
use Game\Infrastructure\Repository\GameRepository;
use Game\Infrastructure\Repository\PlayerRepository;

class JoinGameService
{
    private $gameRepository;

    private $playerRepository;

    public function __construct(GameRepository $gameRepository, PlayerRepository $playerRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
    }

    public function joinGame(SetCompetitorDTO $setCompetitorDTO): void
    {
        $gameId = $setCompetitorDTO->getGameId();
        $game = $this->gameRepository->findById($gameId);
        if (!$game) {
            throw new GameNotFoundException($gameId);
        }

        $playerId = $setCompetitorDTO->getPlayerId();
        $player = $this->playerRepository->findById($playerId);
        if (!$player) {
            throw new PlayerNotFoundException($playerId);
        }

        $game->setCompetitor($player);

        $this->gameRepository->save($game);
    }
}
