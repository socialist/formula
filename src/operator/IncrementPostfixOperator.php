<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ComplexOperatorExpression;
use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\type\IntegerValue;

/**
 * @author Timo Lehnertz
 */
class IncrementPostfixOperator implements ParsedOperator {

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $additionOperator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $additionExpression = new OperatorExpression($leftExpression, $additionOperator, new ConstantExpression(new IntegerValue(1)));
    $assignmentOperator = new ImplementableOperator(ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL);
    return new ComplexOperatorExpression($leftExpression, $assignmentOperator, $additionExpression, $leftExpression, $this, $rightExpression);
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::PostfixOperator;
  }

  public function getPrecedence(): int {
    return 2;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '++';
  }
}
