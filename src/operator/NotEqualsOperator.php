<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ComplexOperatorExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;

/**
 * @author Timo Lehnertz
 */
class NotEqualsOperator implements ParsedOperator {

  public function __construct() {}

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $comparisonOperator = new ImplementableOperator(ImplementableOperator::TYPE_EQUALS);
    $operatorExpression = new OperatorExpression($leftExpression, $comparisonOperator, $rightExpression);
    $notOperator = new ImplementableOperator(ImplementableOperator::TYPE_LOGICAL_NOT);
    return new ComplexOperatorExpression(null, $notOperator, $operatorExpression, $leftExpression, $this, $rightExpression);
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::InfixOperator;
  }

  public function getPrecedence(): int {
    return 10;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '!=';
  }
}
