<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\Number;
use src\expression\NoExpression;

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
    if($left instanceof NoExpression) {
      return (new Number(0))->subtract($right);
    } else {      
      return $left->subtract($right);
    }
  }
}