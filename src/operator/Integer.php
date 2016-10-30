<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:27
 */

namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Integer extends Expression
{
    public function calculate( Operator $context )
    {
        return ( int ) $this->value;
    }
}