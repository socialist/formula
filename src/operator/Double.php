<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:39
 */

namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Double extends Expression
{
    public function calculate( Operator $context )
    {
        $this->value = str_replace( ',', '.', $this->value );
        return ( float ) $this->value;
    }
}