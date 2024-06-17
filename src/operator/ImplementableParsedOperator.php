<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;

/**
 * @author Timo Lehnertz
 */
class ImplementableParsedOperator implements ParsedOperator {

  /**
   * @var ImplementableOperator::TYPE_*
   */
  private readonly int $implementableID;

  private readonly string $identifier;

  private readonly OperatorType $operatorType;

  private readonly int $precedence;

  /**
   * @param ImplementableOperator::TYPE_* $implementableID
   */
  public function __construct(int $implementableID, string $identifier, OperatorType $operatorType, int $precedence) {
    $this->implementableID = $implementableID;
    $this->identifier = $identifier;
    $this->operatorType = $operatorType;
    $this->precedence = $precedence;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function getOperatorType(): OperatorType {
    return $this->operatorType;
  }

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    return new OperatorExpression($leftExpression, new ImplementableOperator($this->implementableID), $rightExpression);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }
}
