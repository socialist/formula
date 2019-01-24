<?php

use PHPUnit\Framework\TestCase;
use socialist\formula\expression\Increment;
use socialist\formula\operator\Double;

class DoubleTest extends TestCase
{
    public function testIsFloat()
    {
        $expression = new Double('2,56');
        $this->assertInternalType(
            'float',
            $expression->calculate(new Increment($expression, $expression)),
            'This expression is not an FLOAT type'
        );
        $this->assertEquals($expression->calculate(new Increment($expression, $expression)), 2.56);
    }
}
