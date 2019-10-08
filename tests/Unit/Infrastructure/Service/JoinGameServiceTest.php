<?php

namespace Tests\Unit\Infrastructure\Service;

use Game\Infrastructure\Persistance\Eloquent\User;
use Game\Infrastructure\Repository\Eloquent\GameRepository;
use Game\Domain\Exception\{
    GameNotFoundException,
    PlayerNotFoundException
};
use Game\Infrastructure\DTO\SetCompetitorDTO;
use Game\Infrastructure\Persistance\Eloquent\Game;
use Game\Infrastructure\Service\JoinGameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinGameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testJoinGameWrongGame()
    {
        $dto = new SetCompetitorDTO(
            'wrong-game-id',
            'wrong-competitor-id'
        );

        $this->expectException(GameNotFoundException::class);

        resolve(JoinGameService::class)->joinGame($dto);
    }

    public function testJoinGameWrongPlayer()
    {
        $eloquentGame = factory(Game::class)->create();

        $dto = new SetCompetitorDTO(
            $eloquentGame->id,
            'wrong-competitor-id'
        );

        $this->expectException(PlayerNotFoundException::class);

        resolve(JoinGameService::class)->joinGame($dto);
    }

    public function testJoinGame()
    {
        $eloquentGame = factory(Game::class)->create();
        $eloquentCompetitor = factory(User::class)->create();

        $dto = new SetCompetitorDTO(
            $eloquentGame->id,
            $eloquentCompetitor->id
        );

        resolve(JoinGameService::class)->joinGame($dto);

        $game = resolve(GameRepository::class)->findById($eloquentGame->id);

        $this->assertNotNull($game->getCompetitor());
        $this->assertEquals($eloquentCompetitor->id, $game->getCompetitor()->getId());
    }
}
