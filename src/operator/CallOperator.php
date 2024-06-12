<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\CallExpression;

/**
 * @author Timo Lehnertz
 */
class CallOperator extends ImplementableOperator implements CoupledOperator {

  private readonly CallExpression $args;

  public function __construct(CallExpression $args) {
    parent::__construct(Operator::IMPLEMENTABLE_CALL);
    $this->args = $args;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '('.$this->args->toString($prettyPrintOptions).')';
  }

  public function getCoupledExpression(): CallExpression {
    return $this->args;
  }
}
