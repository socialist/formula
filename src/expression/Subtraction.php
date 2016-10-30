<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 29.10.16
 * Time: 17:20
 */

namespace socialist\formula\expression;

class Subtraction extends Operator
{
    public function doCalculate($left, $right)
    {
        return $left - $right;
    }
}