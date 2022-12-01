<?php
namespace TimoLehnertz\formula\expression;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Division extends Operator {

  public function __construct() {
    parent::__construct(1);
  }
  
  public function doCalculate($left, $right) {
    if(!is_numeric($left) || !is_numeric($right)) throw new \InvalidArgumentException("Can only divide numbers");
    return $right == 0 ? NAN : $left / $right;
  }
}