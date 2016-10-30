<?php

/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 17:14
 */
class VariableTest extends PHPUnit_Framework_TestCase
{
    public function testVariable()
    {
        $expression = new \socialist\formula\operator\Variable( 'p', '2,56' );
        $this->assertEquals( $expression->calculate( new \socialist\formula\expression\Increment( $expression, $expression ) ), 2.56 );
    }
}
