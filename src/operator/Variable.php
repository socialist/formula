<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 17:10
 */

namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Variable extends Expression
{
    protected $key;

    public function __construct( $key, $value = 0 )
    {
        $this->key = $key;
        parent::__construct( $value );
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param Operator $operator
     */
    public function calculate( Operator $operator )
    {
        $this->value = str_replace( ',', '.', $this->value );
        return ( float ) $this->value;
    }
}