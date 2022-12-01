<?php
namespace socialistFork\formula\operator;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Subtraction extends Operator {

  public function __construct() {
    parent::__construct(0, false);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return $left->subtract($right);
  }
}