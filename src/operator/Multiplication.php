<?php
namespace TimoLehnertz\formula\operator;


/**
 *
 * @author Timo Lehnertz
 *
 */
class Multiplication extends Operator {

  public function __construct() {
    parent::__construct('*', 1, true);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return $left->multiply($right);
  }
}