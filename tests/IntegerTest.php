<?php

/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:28
 */
class IntegerTest extends PHPUnit_Framework_TestCase
{
    public function testIsInteger()
    {
        $expression = new \socialist\formula\operator\Integer('23');
        $this->assertInternalType( 'integer', $expression->calculate( new \socialist\formula\expression\Increment( $expression, $expression ) ), 'This expression is not an integer type' );
    }
}
