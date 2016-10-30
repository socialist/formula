<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:19
 */

namespace socialist\formula\operator;

use socialist\formula\expression\Operator;


abstract class Expression
{
    protected $value;

    public function __construct( $value )
    {
        $this->value = $value;
    }

    public abstract function calculate( Operator $context );
}