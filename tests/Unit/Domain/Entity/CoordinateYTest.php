<?php

namespace Tests\Unit\Domain\Entity;

use Game\Domain\Entity\CoordinateY;
use Game\Domain\Exception\WrongCoordinateYException;
use Tests\TestCase;

class CoordinateYTest extends TestCase
{
    public function testCoordinateYMinWrongValue()
    {
        $this->expectException(WrongCoordinateYException::class);

        new CoordinateY(-1);
    }

    public function testCoordinateYMaxWrongValue()
    {
        $this->expectException(WrongCoordinateYException::class);

        new CoordinateY(3);
    }

    public function testCoordinateY()
    {
        $y = new CoordinateY(1);

        $this->assertEquals($y->getValue(), 1);
    }
}
