<?php
namespace TimoLehnertz\formula\operator;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Subtraction extends Operator {

  public function __construct() {
    parent::__construct('-', 6, false, false, true);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return $left->subtract($right);
  }
}