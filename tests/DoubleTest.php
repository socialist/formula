<?php

/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:41
 */
class DoubleTest extends PHPUnit_Framework_TestCase
{
    public function testIsFloat()
    {
        $expression = new \socialist\formula\operator\Double( '2,56' );
        $this->assertInternalType( 'float', $expression->calculate( new \socialist\formula\expression\Increment( $expression, $expression ) ), 'This expression is not an FLOAT type' );
        $this->assertEquals( $expression->calculate( new \socialist\formula\expression\Increment( $expression, $expression ) ), 2.56 );
    }
}
