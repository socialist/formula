<?php
namespace socialist\formula\expression;

class Subtraction extends Operator
{
    /**
     * @inheritdoc
     */
    public function doCalculate(float $left, float $right): float
    {
        return $left - $right;
    }
}