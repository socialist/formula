<?php

use PHPUnit\Framework\TestCase;
use socialist\formula\expression\Increment;
use socialist\formula\operator\Integer;
use socialist\formula\operator\Percent;

class PercentTest extends TestCase
{
    public function testPercentExpression()
    {
        $integer = new Integer('45');
        $percent = new Percent('15%');
        $increment = new Increment($integer, $percent);
        $this->assertEquals(51.75, $increment->calculate($increment));

        $increment = new Increment($percent, $integer);
        $this->assertEquals(45.15, $increment->calculate($increment));
    }
}
