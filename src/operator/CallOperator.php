<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ArgumentListExpression;
use TimoLehnertz\formula\expression\ComplexOperatorExpression;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 */
class CallOperator implements ParsedOperator {

  private ArgumentListExpression $arguments;

  public function __construct(ArgumentListExpression $arguments) {
    $this->arguments = $arguments;
  }

  public function getPrecedence(): int {
    return 2;
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::PostfixOperator;
  }

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $arrayAccsessOperator = new ImplementableOperator(ImplementableOperator::TYPE_CALL);
    return new ComplexOperatorExpression($leftExpression, $arrayAccsessOperator, $this->arguments, $leftExpression, $this, null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->arguments->toString($prettyPrintOptions);
  }
}
