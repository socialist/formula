<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\SubFormula;

/**
 * 
 * @author Timo Lehnertz
 *
 * Placeholder expression
 */
class NoExpression implements Expression, SubFormula {

  public function calculate(): Calculateable {
    return new Number(0, true);
  }
  
  public function toString(): string {
    return '';
  }
}