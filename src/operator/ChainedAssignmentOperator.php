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
class ChainedAssignmentOperator extends ParsedOperator {

  private readonly ImplementableOperator $chainedOperator;

  private readonly int $precedence;

  private readonly string $identifier;

  public function __construct(ImplementableOperator $chainedOperator, int $precedence, string $identifier) {
    parent::__construct();
    $this->chainedOperator = $chainedOperator;
    $this->precedence = $precedence;
  }

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $operatorExpression = new OperatorExpression($leftExpression, $this->chainedOperator, $rightExpression);
    $assignmentOperator = new ImplementableOperator(ImplementableOperator::TYPE_DIRECT_ASSIGNMENT);
    return new ComplexOperatorExpression($leftExpression, $assignmentOperator, $operatorExpression, $leftExpression, $this, $rightExpression);
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::InfixOperator;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }
}
