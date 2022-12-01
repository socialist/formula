<?php
namespace socialistFork\formula\expression;

use socialistFork\formula\operator\Operator;
use socialistFork\formula\operator\Calculateable;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class AndOperator extends Operator {

  /**
   * @see \socialistFork\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right) {
    
  }
}

