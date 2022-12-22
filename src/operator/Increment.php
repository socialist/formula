<?php
namespace TimoLehnertz\formula\operator;



/**
 *
 * @author Timo Lehnertz
 *
 */
class Increment extends Operator {

  public function __construct() {
    parent::__construct('+', 0, true);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return $left->add($right);
  }
}