<?php

namespace Tests\Unit\Domain\Entity;

use Game\Domain\Entity\{
    Game,
    Player,
    Step,
    CoordinateX,
    CoordinateY
};
use Game\Domain\Exception\{
    CompetitorAndOwnerCannotBeEqualException,
    CompetitorIsMissedException,
    GameAlreadyEndedException,
    GameAlreadyHasCompetitorException,
    GameAlreadyHasWinnerException,
    GameAlreadyStartedException,
    GameCannotBeEndedWithoutStartingException,
    GameHasNotStartedYetException,
    GameIsFullOfStepsException,
    PlayerIsNotAPlayerOfThisGameException
};
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

        $this->assertEquals('test-id', $game->getId());
        $this->assertEquals($this->player, $game->getOwner());
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

    public function testPlayerIsAbleToMakeAFirstMove()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->assertFalse($game->playerIsAbleToMakeAMove($competitor));
        $this->assertTrue($game->playerIsAbleToMakeAMove($this->player));
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

    public function testGetPlayerOfGameWrongUser()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $wrongPlayer = Player::createNew(Str::uuid());

        $this->expectException(PlayerIsNotAPlayerOfThisGameException::class);

        $game->getPlayerOfGame($wrongPlayer);
    }

    public function testGetPlayerOfGame()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $player = $game->getPlayerOfGame($this->player);
        $this->assertEquals($player, $this->player);
    }

    public function testIsStepUniqueWithoutCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $this->expectException(CompetitorIsMissedException::class);
        $game->isStepUnique($step);
    }

    public function testIsStepUnique()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $game->setCompetitor($competitor);

        $this->assertTrue($game->isStepUnique($step));
    }

    public function testGetId()
    {
        $game = Game::createGame('test-id', $this->player);

        $this->assertEquals('test-id', $game->getId());
    }

    public function testGetOwner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertEquals($this->player, $game->getOwner());
    }

    public function testGetCompetitorWithoutCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertNull($game->getCompetitor());
    }

    public function testGetCompetitor()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);

        $this->assertEquals($competitor, $game->getCompetitor());
    }

    public function testGetWinnerWithNoWinner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertNull($game->getWinner());
    }

    public function testGetWinner()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->setWinner($competitor);

        $this->assertEquals($competitor, $game->getWinner());
    }

    public function testGetStartedAtWithoutStarting()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertNull($game->getStartedAt());
    }

    public function testGetStartedAt()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();

        $this->assertNotNull($game->getStartedAt());
    }

    public function testGetEndedAtWithoutEnding()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertNull($game->getEndedAt());
    }

    public function testGetEndedAt()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->endGame();

        $this->assertNotNull($game->getEndedAt());
    }

    public function testGetStepsCountWithoutAny()
    {
        $game = Game::createGame(Str::uuid(), $this->player);

        $this->assertEquals(0, $game->getStepsCount());
    }

    public function testGetStepsCount()
    {
        $game = Game::createGame(Str::uuid(), $this->player);
        $competitor = Player::createNew(Str::uuid());

        $game->setCompetitor($competitor);
        $game->startGame();
        $game->incrementStepsCount();

        $this->assertEquals(1, $game->getStepsCount());
    }
}
