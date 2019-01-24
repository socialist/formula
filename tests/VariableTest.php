<?php

use PHPUnit\Framework\TestCase;
use socialist\formula\expression\Increment;
use socialist\formula\operator\Variable;

class VariableTest extends TestCase
{
    public function testVariable()
    {
        $expression = new Variable('p', '2,56');
        $this->assertEquals($expression->calculate(new Increment($expression, $expression)), 2.56);
    }
}
