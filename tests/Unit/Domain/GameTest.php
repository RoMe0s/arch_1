<?php

namespace Tests\Unit\Domain;

use Game\Domain\Entity\Game;
use Game\Domain\Entity\Player;
use Game\Domain\Exception\CompetitorAndOwnerCannotBeEqualException;
use Game\Domain\Exception\CompetitorIsMissedException;
use Game\Domain\Exception\GameAlreadyEndedException;
use Game\Domain\Exception\GameAlreadyHasCompetitorException;
use Game\Domain\Exception\GameAlreadyHasWinnerException;
use Game\Domain\Exception\GameAlreadyStartedException;
use Game\Domain\Exception\GameCannotBeEndedWithoutStartingException;
use Game\Domain\Exception\GameHasNotStartedYetException;
use Game\Domain\Exception\GameIsFullOfStepsException;
use Game\Domain\Exception\PlayerIsNotAPlayerOfThisGameException;
use Illuminate\Support\Str;
use Tests\TestCase;

class GameTest extends TestCase
{
    private $player;

    public function setUp(): void
    {
        parent::setUp();

        $this->player = Player::createNew(Str::uuid());
    }

    public function testCreateGame()
    {
        $game = Game::createGame('test-id', $this->player);

        $this->assertEquals($game->getId(), 'test-id');
        $this->assertEquals($game->getOwner(), $this->player);
    }

    public function testSetCompetitorEqualToOwner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->expectException(CompetitorAndOwnerCannotBeEqualException::class);

        try {
            $game->setCompetitor($this->player);
        } finally {
            $this->assertNull($game->getCompetitor());
        }
    }

    public function testSetCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);

        $this->assertEquals($game->getCompetitor(), $competitor);
    }

    public function testSetCompetitorTwice()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);

        $this->expectException(GameAlreadyHasCompetitorException::class);

        $game->setCompetitor($competitor);
    }

    public function testStartGameWithoutCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->expectException(CompetitorIsMissedException::class);

        try {
            $game->startGame();
        } finally {
            $this->assertNull($game->getStartedAt());
        }
    }

    public function testStartGame()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->assertNotNull($game->getStartedAt());
    }

    public function testStartGameTwice()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->expectException(GameAlreadyStartedException::class);

        $game->startGame();
    }

    public function testEndGameWithoutStarting()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());
        $game->setCompetitor($competitor);

        $this->expectException(GameCannotBeEndedWithoutStartingException::class);

        try {
            $game->endGame();
        } finally {
            $this->assertNull($game->getEndedAt());
        }
    }

    public function testEndGame()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->endGame();

        $this->assertNotNull($game->getEndedAt());
    }

    public function testEndGameTwice()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->endGame();

        $this->expectException(GameAlreadyEndedException::class);

        $game->endGame();
    }

    public function testIncrementStepsCountWithoutStarting()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->expectException(GameHasNotStartedYetException::class);

        try {
            $game->incrementStepsCount();
        } finally {
            $this->assertEquals(0, $game->getStepsCount());
        }
    }

    public function testIncrementStepsCountOverflow()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->expectException(GameIsFullOfStepsException::class);

        try {
            for ($stepNo = 0; $stepNo <= Game::MAX_COUNT_OF_STEPS; $stepNo++) {
                $game->incrementStepsCount();
            }
        } finally {
            $this->assertEquals(Game::MAX_COUNT_OF_STEPS, $game->getStepsCount());
        }
    }

    public function testIncrementStepsCountAlreadyEnded()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->endGame();

        $this->expectException(GameAlreadyEndedException::class);

        try {
            $game->incrementStepsCount();
        } finally {
            $this->assertEquals(0, $game->getStepsCount());
        }
    }

    public function testIncrementStepsCount()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->incrementStepsCount();

        $this->assertEquals(1, $game->getStepsCount());
    }

    public function testSetWinner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $game->setWinner($this->player);

        $this->assertEquals($this->player->getId(), $game->getWinner()->getId());
    }

    public function testSetWinnerTwice()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $game->setWinner($this->player);

        $this->expectException(GameAlreadyHasWinnerException::class);

        $game->setWinner($this->player);
    }

    public function testPlayerIsTheOwner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $anotherPlayer = Player::createNew(Str::uuid());

        $this->assertFalse($game->playerIsTheOwner($anotherPlayer));
        $this->assertTrue($game->playerIsTheOwner($this->player));
    }

    public function testPlayerIsParticipant()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $anotherPlayer = Player::createNew(Str::uuid());

        $this->assertFalse($game->playerIsParticipant($anotherPlayer));
        $this->assertTrue($game->playerIsParticipant($this->player));
    }

    public function testPlayerIsAbleToMakeAMoveWithoutCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->expectException(CompetitorIsMissedException::class);

        $game->playerIsAbleToMakeAMove($this->player);
    }

    public function testPlayerIsAbleToMakeAMoveWithoutStarting()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);

        $this->expectException(GameHasNotStartedYetException::class);

        $game->playerIsAbleToMakeAMove($this->player);
    }

    public function testPlayerIsAbleToMakeAMoveWrongPlayer()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());
        $anotherPlayer = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->expectException(PlayerIsNotAPlayerOfThisGameException::class);

        $game->playerIsAbleToMakeAMove($anotherPlayer);
    }

    public function testPlayerIsAbleToMakeAMove()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->assertTrue($game->playerIsAbleToMakeAMove($this->player));
        $this->assertFalse($game->playerIsAbleToMakeAMove($competitor));
    }

    public function testPlayerIsAbleToMakeAMoveAfterEnding()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->endGame();

        $this->expectException(GameAlreadyEndedException::class);

        $game->playerIsAbleToMakeAMove($this->player);
    }
}
