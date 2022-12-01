<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\Calculateable;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class AndOperator extends Operator {

  /**
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right) {
    
  }
}

