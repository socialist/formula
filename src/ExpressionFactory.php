<?php
namespace socialist\formula;


use socialist\formula\operator\Double;
use socialist\formula\operator\Integer;
use socialist\formula\operator\Percent;
use socialist\formula\operator\Variable;

class ExpressionFactory
{
    public static function factory(string $expression, $variables = [])
    {
        if ( strpos( $expression, '.' ) !== false || strpos( $expression, ',' ) !== false ) {
            return new Double( $expression );
        } else if ( strpos( $expression, '%' ) !== false ) {
            return new Percent( $expression );
        } else if ( ( int ) $expression > 0 ) {
            return new Integer( $expression );
        } else {
            $variable = new Variable( $expression );

            if ( array_key_exists( $expression, $variables ) ) {
                $variable->setValue( $variables[ $expression ] );
            }

            return $variable;
        }
    }
}