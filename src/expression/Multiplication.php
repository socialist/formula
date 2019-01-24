<?php
namespace socialist\formula\expression;


class Multiplication extends Operator
{
    /**
     * @inheritdoc
     */
    public function doCalculate(float $left, float $right): float
    {
        return $left * $right;
    }
}