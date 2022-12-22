<?php
namespace TimoLehnertz\formula\operator;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Division extends Operator {

  public function __construct() {
    parent::__construct('/', 1, false);
  }
  
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return $left->divide($right);
  }
}