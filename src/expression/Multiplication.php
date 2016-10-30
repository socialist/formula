<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 18:52
 */

namespace socialist\formula\expression;


class Multiplication extends Operator
{
    public function doCalculate($left, $right)
    {
        return $left * $right;
    }
}