<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 18:51
 */

namespace socialist\formula\expression;


class Division extends Operator
{
    public function doCalculate( $left, $right )
    {
        return round( $left / $right, 2 );
    }
}