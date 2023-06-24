<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\BooleanExpression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class OrOperator extends Operator {

  public function __construct() {
    parent::__construct('||', 15, true, true);
  }
  
  /**
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->isTruthy() || $right->calculate()->isTruthy());
  }
}