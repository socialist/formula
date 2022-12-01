<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\BooleanExpression;

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
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression(!$right->calculate()->isTruthy());
  }
}

