<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:47
 */

namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Percent extends Expression
{
    public function __construct( $value )
    {
        $value = str_replace( '%', '', $value );
        parent::__construct($value);
    }

    public function calculate( Operator $context )
    {
        if ( $context->getLeftOperator() instanceof Percent ) {
            return round( ( $this->value / 100 ), 2);
        } else if ( $context->getRightOperator() instanceof Percent ) {
            return round( ( ( $this->value / 100 ) * $context->getLeftOperator()->calculate( $context ) ), 2);
        }

        throw new \Exception( "This object must be the left or the right operand of OperatorExpression object" );
    }
}