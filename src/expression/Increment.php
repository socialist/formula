<?php
namespace socialist\formula\expression;


class Increment extends Operator
{
    protected function doCalculate(float $left, float $right): float
    {
        return $left + $right;
    }
}