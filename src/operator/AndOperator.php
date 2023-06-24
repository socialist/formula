<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\BooleanExpression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class AndOperator extends Operator {

  public function __construct() {
    parent::__construct('&&', 14, true, true);
  }
  
  /**
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->isTruthy() && $right->calculate()->isTruthy());
  }
}

