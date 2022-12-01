<?php
namespace socialistFork\formula\operator;

use socialistFork\formula\expression\BooleanExpression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class OrOperator extends Operator {

  public function __construct() {
    parent::__construct(1, true, true);
  }
  
  /**
   * @see \socialistFork\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->isTruthy() || $right->calculate()->isTruthy());
  }
}

