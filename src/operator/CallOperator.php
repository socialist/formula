<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\ExpressionListExpression;

/**
 * @author Timo Lehnertz
 */
class CallOperator extends ImplementableOperator implements CoupledOperator {

  private readonly ExpressionListExpression $args;

  public function __construct(ExpressionListExpression $args) {
    parent::__construct(Operator::IMPLEMENTABLE_CALL);
    $this->args = $args;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '('.$this->args->toString($prettyPrintOptions).')';
  }

  public function getCoupledExpression(): Expression {
    return $this->args;
  }
}
