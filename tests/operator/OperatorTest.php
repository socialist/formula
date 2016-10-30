<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 17:22
 */

namespace operator;


class OperatorTest extends \PHPUnit_Framework_TestCase
{
    function testSubtraction()
    {
        $integer = new \socialist\formula\operator\Integer( '45' );
        $percent = new \socialist\formula\operator\Percent( '15%' );
        $increment = new \socialist\formula\expression\Subtraction( $integer, $percent );
        $this->assertEquals( 38.25, $increment->calculate( $increment ) );
    }

    function testIncrement()
    {
        $integer = new \socialist\formula\operator\Integer( '45' );
        $percent = new \socialist\formula\operator\Integer( '15' );
        $increment = new \socialist\formula\expression\Increment( $integer, $percent );
        $this->assertEquals( 60, $increment->calculate( $increment ) );
    }

    function testDivision()
    {
        $integer = new \socialist\formula\operator\Integer( '45' );
        $double  = new \socialist\formula\operator\Integer( '15' );
        $increment = new \socialist\formula\expression\Division( $integer, $double );
        $this->assertEquals( 3, $increment->calculate( $increment ) );
    }

    function testMultiple()
    {
        $integer1 = new \socialist\formula\operator\Integer( '45' );
        $integer2 = new \socialist\formula\operator\Integer( '15' );
        $increment = new \socialist\formula\expression\Multiplication( $integer1, $integer2 );
        $this->assertEquals( 675, $increment->calculate( $increment ) );
    }
}
