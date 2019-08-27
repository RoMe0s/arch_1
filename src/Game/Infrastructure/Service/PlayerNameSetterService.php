<?php

namespace Game\Infrastructure\Service;

use Game\Domain\Entity\Player;
use Game\Infrastructure\DTO\SetPlayerNameDTO;
use Game\Infrastructure\Repository\GameRepository;

class PlayerNameSetterService
{
    private $gameRepository;

    function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    public function setPlayerName(SetPlayerNameDTO $setPlayerNameDTO)
    {
        $game = $this->gameRepository->findById($setPlayerNameDTO->getGameId());
        $somePlayer = Player::createNew($setPlayerNameDTO->getPlayerId());
        $player = $game->getPlayerOfGame($somePlayer);

        $player->setName($setPlayerNameDTO->getPlayerName());

        $this->gameRepository->save($game);
    }
}
