<?php
namespace socialist\formula\expression;


class Division extends Operator
{
    /**
     * @inheritdoc
     */
    public function doCalculate(float $left, float $right): float
    {
        return $right == 0 ? NAN : $left / $right;
    }
}