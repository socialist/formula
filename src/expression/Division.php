<?php
namespace socialist\formula\expression;


class Division extends Operator
{
    /**
     * @inheritdoc
     */
    public function doCalculate(float $left, float $right): float
    {
        return round($left / $right, 2);
    }
}