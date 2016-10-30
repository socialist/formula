<?php

/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 2:10
 */
class PercentTest extends PHPUnit_Framework_TestCase
{
    public function testPercentExpression()
    {
        $integer = new \socialist\formula\operator\Integer( '45' );
        $percent = new \socialist\formula\operator\Percent( '15%' );
        $increment = new \socialist\formula\expression\Increment( $integer, $percent );
        $this->assertEquals( 51.75, $increment->calculate( $increment ) );

        $increment = new \socialist\formula\expression\Increment( $percent, $integer );
        $this->assertEquals( 45.15, $increment->calculate( $increment ) );
    }
}
