<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\ComplexOperatorExpression;
use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;

/**
 * @author Timo Lehnertz
 */
class DecrementPrefixOperator implements ParsedOperator {

  public function __construct() {}

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $subtractionOperator = new ImplementableOperator(ImplementableOperator::TYPE_SUBTRACTION);
    $subtractionExpression = new OperatorExpression($rightExpression, $subtractionOperator, new ConstantExpression(new IntegerType(true), new IntegerValue(1), '1'));
    $assignmentOperator = new ImplementableOperator(ImplementableOperator::TYPE_DIRECT_ASSIGNMENT);
    return new ComplexOperatorExpression($rightExpression, $assignmentOperator, $subtractionExpression, $leftExpression, $this, $rightExpression);
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::PrefixOperator;
  }

  public function getPrecedence(): int {
    return 3;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '--';
  }
}
