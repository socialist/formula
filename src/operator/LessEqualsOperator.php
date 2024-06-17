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
class LessEqualsOperator implements ParsedOperator {

  public function __construct() {}

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $comparisonOperator = new ImplementableOperator(ImplementableOperator::TYPE_EQUALS);
    $comparisonExpression = new OperatorExpression($leftExpression, $comparisonOperator, $rightExpression);
    $lessOperator = new ImplementableOperator(ImplementableOperator::TYPE_LESS);
    $lessExpression = new OperatorExpression($leftExpression, $lessOperator, $rightExpression);
    $orOperator = new ImplementableOperator(ImplementableOperator::TYPE_LOGICAL_OR);
    return new ComplexOperatorExpression($comparisonExpression, $orOperator, $lessExpression, $leftExpression, $this, $rightExpression);
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::InfixOperator;
  }

  public function getPrecedence(): int {
    return 9;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '<=';
  }
}