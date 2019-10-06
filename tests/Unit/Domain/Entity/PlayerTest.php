<?php

namespace Tests\Unit\Domain\Entity;

use Game\Domain\Entity\{
    CoordinateX,
    CoordinateY,
    Player,
    Step
};
use Game\Domain\Exception\{
    PlayerNameAlreadySetException,
    PlayerIsFullOfStepsException
};
use Illuminate\Support\Str;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    public function testCreateNew()
    {
        $player = Player::createNew('test-id');

        $this->assertEquals('test-id', $player->getId());
        $this->assertEquals([], $player->getSteps());
        $this->assertNull($player->getName());
    }

    public function testSetName()
    {
        $player = Player::createNew(Str::uuid());

        $player->setName('test-name');

        $this->assertEquals('test-name', $player->getName());
    }

    public function testSetNameTwice()
    {
        $player = Player::createNew(Str::uuid());

        $player->setName('test-name');

        $this->expectException(PlayerNameAlreadySetException::class);

        $player->setName('test-name');
    }

    public function testAddStep()
    {
        $player = Player::createNew(Str::uuid());
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $player->addStep($step);

        $this->assertEquals([$step], $player->getSteps());
    }

    public function testAddStepOverflow()
    {
        $player = Player::createNew(Str::uuid());

        $this->expectException(PlayerIsFullOfStepsException::class);

        try {
            for ($stepNo = 0; $stepNo <= 5; $stepNo++) {
                $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));
                $player->addStep($step);
            }
        } finally {
            $this->assertEquals(5, count($player->getSteps()));
        }
    }

    public function testIsStepExistWrongStep()
    {
        $player = Player::createNew(Str::uuid());
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $this->assertEquals(false, $player->isStepExist($step));
    }

    public function testIsStepExist()
    {
        $player = Player::createNew(Str::uuid());
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $player->addStep($step);

        $this->assertEquals(true, $player->isStepExist($step));
    }

    public function testIsLastActed()
    {
        $player = Player::createNew(Str::uuid());

        $this->assertEquals(false, $player->isLastActed());
    }

    public function testGetId()
    {
        $player = Player::createNew('test-id');

        $this->assertEquals('test-id', $player->getId());
    }

    public function testGetNameWithoutName()
    {
        $player = Player::createNew(Str::uuid());

        $this->assertNull($player->getName());
    }

    public function testGetName()
    {
        $player = Player::createNew(Str::uuid());

        $player->setName('test-name');

        $this->assertEquals('test-name', $player->getName());
    }

    public function testGetStepsWithoutSteps()
    {
        $player = Player::createNew(Str::uuid());

        $this->assertEquals([], $player->getSteps());
    }

    public function testGetSteps()
    {
        $player = Player::createNew(Str::uuid());
        $step = new Step(Str::uuid(), new CoordinateX(1), new CoordinateY(2));

        $player->addStep($step);

        $this->assertEquals([$step], $player->getSteps());
    }
}
