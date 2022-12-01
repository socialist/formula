<?php
namespace socialistFork\formula\operator;

use socialistFork\formula\expression\BooleanExpression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class SmallerOperator extends Operator {

  public function __construct() {
    parent::__construct(0, false, true);
  }
  
  /**
   * @see \socialistFork\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->getValue() < $right->calculate()->getValue());
  }
}

