<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ComplexOperatorExpression;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 */
class ArrayAccessOperator extends ParsedOperator {

  private Expression $indexExpression;

  public function __construct(Expression $indexExpression) {
    parent::__construct();
    $this->indexExpression = $indexExpression;
  }

  public function getPrecedence(): int {
    return 2;
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::PostfixOperator;
  }

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $arrayAccsessOperator = new ImplementableOperator(ImplementableOperator::TYPE_ARRAY_ACCESS);
    return new ComplexOperatorExpression($leftExpression, $arrayAccsessOperator, $rightExpression, $leftExpression, $this, $rightExpression);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '['.$this->indexExpression->toString($prettyPrintOptions).']';
  }
}
