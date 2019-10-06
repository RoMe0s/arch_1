<?php

namespace Tests\Unit\Domain\Entity;

use Game\Domain\Entity\{
    CoordinateX,
    CoordinateY,
    Step
};

use Illuminate\Support\Str;
use Tests\TestCase;

class StepTest extends TestCase
{
    public function testStep()
    {
        $x = new CoordinateX(1);
        $y = new CoordinateY(2);
        $step = new Step('test-id', $x, $y);

        $this->assertEquals('test-id', $step->getId());
        $this->assertEquals($x, $step->getX());
        $this->assertEquals($y, $step->getY());
    }
}
