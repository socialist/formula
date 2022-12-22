<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\operator\Operator;

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

