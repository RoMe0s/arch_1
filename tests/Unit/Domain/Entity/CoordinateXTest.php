<?php

namespace Tests\Unit\Domain\Entity;

use Game\Domain\Entity\CoordinateX;
use Game\Domain\Exception\WrongCoordinateXException;
use Tests\TestCase;

class CoordinateXTest extends TestCase
{
    public function testCoordinateXMinWrongValue()
    {
        $this->expectException(WrongCoordinateXException::class);

        new CoordinateX(-1);
    }

    public function testCoordinateXMaxWrongValue()
    {
        $this->expectException(WrongCoordinateXException::class);

        new CoordinateX(3);
    }

    public function testCoordinateX()
    {
        $x = new CoordinateX(1);

        $this->assertEquals($x->getValue(), 1);
    }
}
