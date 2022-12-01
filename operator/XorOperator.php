<?php
namespace socialistFork\formula\operator;

use socialistFork\formula\expression\BooleanExpression;

/**
 *
 * @author Timo Lehnertz
 *
 */
class XorOperator extends Operator {

  public function __construct() {
    parent::__construct(1, true);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->isTruthy() ^ $right->calculate()->isTruthy());
  }
}