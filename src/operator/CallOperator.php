<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ArgumentListExpression;

/**
 * @author Timo Lehnertz
 */
class CallOperator extends ImplementableOperator implements CoupledOperator {

  private readonly ArgumentListExpression $args;

  public function __construct(ArgumentListExpression $args) {
    parent::__construct(Operator::IMPLEMENTABLE_CALL);
    $this->args = $args;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '('.$this->args->toString($prettyPrintOptions).')';
  }

  public function getCoupledExpression(): ArgumentListExpression {
    return $this->args;
  }
}
