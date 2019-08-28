<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Entity\Player;
use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerNotFoundException
};
use Game\Infrastructure\DTO\SetPlayerNameDTO;
use Game\Infrastructure\Repository\{
    GameRepository,
    PlayerRepository
};

class PlayerNameSetterService
{
    private $gameRepository;

    private $playerRepository;

    function __construct(GameRepository $gameRepository, PlayerRepository $playerRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
    }

    public function setPlayerName(SetPlayerNameDTO $setPlayerNameDTO)
    {
        $gameId = $setPlayerNameDTO->getGameId();
        $game = $this->gameRepository->findById($gameId);
        if (!$game) {
            throw new GameNotFoundException($gameId);
        }

        $playerId = $setPlayerNameDTO->getPlayerId();
        $somePlayer = Player::createNew($playerId);
        if (!$somePlayer) {
            throw new PlayerNotFoundException($playerId);
        }

        $player = $game->getPlayerOfGame($somePlayer);
        $player->setName($setPlayerNameDTO->getPlayerName());

        $this->gameRepository->save($game);
    }
}
