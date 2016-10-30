<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 1:08
 */

namespace socialist\formula\expression;

use socialist\formula\operator\Expression;

abstract class Operator extends Expression
{
    protected $leftOperator;
    protected $rightOperator;

    public function __construct( Expression $left, Expression $right )
    {
        $this->leftOperator = $left;
        $this->rightOperator = $right;
    }

    public function calculate( Operator $context )
    {
        $left = $this->leftOperator->calculate( $this );
        $right = $this->rightOperator->calculate( $this );

        return $this->doCalculate( $left, $right );
    }

    /**
     * @return Expression
     */
    public function getLeftOperator(): Expression
    {
        return $this->leftOperator;
    }

    /**
     * @return Expression
     */
    public function getRightOperator(): Expression
    {
        return $this->rightOperator;
    }

    protected abstract function doCalculate( $left, $right );
}