<?php

use PHPUnit\Framework\TestCase;
use socialist\formula\expression\Increment;
use socialist\formula\operator\Integer;

class IntegerTest extends TestCase
{
    public function testIsInteger()
    {
        $expression = new Integer('23');
        $this->assertInternalType('float',
            $expression->calculate(new Increment($expression, $expression)),
            'This expression is not an integer type'
        );
    }
}
