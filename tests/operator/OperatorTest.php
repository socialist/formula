<?php
namespace operator;

use PHPUnit\Framework\TestCase;
use socialist\formula\expression\Division;
use socialist\formula\expression\Increment;
use socialist\formula\expression\Multiplication;
use socialist\formula\expression\Subtraction;
use socialist\formula\operator\Integer;
use socialist\formula\operator\Percent;

class OperatorTest extends TestCase
{
    function testSubtraction()
    {
        $integer = new Integer( '45' );
        $percent = new Percent( '15%' );
        $increment = new Subtraction( $integer, $percent );
        $this->assertEquals( 38.25, $increment->calculate( $increment ) );
    }

    function testIncrement()
    {
        $integer = new Integer('45');
        $percent = new Integer('15');
        $increment = new Increment($integer, $percent);
        $this->assertEquals(60, $increment->calculate($increment));
    }

    function testDivision()
    {
        $integer = new Integer('45');
        $double  = new Integer('15');
        $increment = new Division($integer, $double);
        $this->assertEquals(3, $increment->calculate($increment));
    }

    function testMultiple()
    {
        $integer1 = new Integer('45');
        $integer2 = new Integer('15');
        $increment = new Multiplication($integer1, $integer2);
        $this->assertEquals(675, $increment->calculate($increment));
    }
}
