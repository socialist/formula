<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator\composed;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\ValidationException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\InfixOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ChainedAssignmentOperator extends InfixOperator {

  private readonly ImplementableOperator $chainedOperator;

  private readonly string $identifier;

  public function __construct(ImplementableOperator $chainedOperator, string $identifier, int $id) {
    parent::__construct(16, $id);
    $this->chainedOperator = $chainedOperator;
    $this->identifier = $identifier;
  }

  protected function validateInfixOperation(Type $leftValue, Type $rightType): Type {
    $assignmentOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT);
    $chainedType = $leftValue->getOperatorResultType($this->chainedOperator, $rightType);
    if($chainedType === null) {
      throw new ValidationException('Invalid operand '.$rightType->getIdentifier());
    }
    $finalType = $leftValue->getOperatorResultType($assignmentOperator, $chainedType);
    if($finalType === null) {
      throw new ValidationException('Can\'t assign '.$rightType->getIdentifier().' with '.$chainedType->getIdentifier());
    }
    return $finalType;
  }

  protected function operateInfix(Value $leftValue, Value $rightValue): Value {
    $additionOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_SUBTRACTION);
    $assignmentOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT);
    $additionResult = $leftValue->operate($additionOperator, $rightValue);
    $result = $leftValue->operate($assignmentOperator, $additionResult);
    return $result;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getCompatibleOperands(Type $leftType): array {
    return $leftType->getCompatibleOperands($this->chainedOperator);
  }
}
