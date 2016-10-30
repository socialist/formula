<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 2:12
 */

namespace socialist\formula\expression;


class Increment extends Operator
{
    protected function doCalculate($left, $right)
    {
        return $left + $right;
    }
}