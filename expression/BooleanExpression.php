<?php
namespace socialistFork\formula\expression;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class BooleanExpression extends Number {
  
  /**
   * @param bool $true
   */
  public function __construct(bool $true) {
    parent::__construct($true ? 1 : 0);
  }
}

