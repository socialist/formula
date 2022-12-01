<?php
namespace socialistFork\formula\operator;

use socialistFork\formula\expression\BooleanExpression;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class NotOperator extends Operator {

  public function __construct() {
    parent::__construct(5, false, false);
  }
  
  /**
   * @see \socialistFork\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression(!$right->calculate()->isTruthy());
  }
}

