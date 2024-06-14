<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
abstract class PrefixOperator implements Operator {
  use OperatorHelper;

  public function getOperatorType(): OperatorType {
    return OperatorType::PrefixOperator;
  }

  public function validateOperation(?Type $leftType, ?Type $rightType): Type {
    if($leftType !== null) {
      throw new \UnexpectedValueException('Expected leftExpression to be null');
    }
    if($rightType === null) {
      throw new FormulaValidationException('Missing right operand!');
    }
    return $this->validatePrefixOperation($rightType);
  }

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    if($rightValue === null) {
      throw new FormulaValidationException('Missing right operand!');
    }
    return $this->operatePrefix($rightValue);
  }

  protected abstract function validatePrefixOperation(Type $rightType): Type;

  protected abstract function operatePrefix(Value $rightValue): Value;

  public function getCompatibleOperands(Type $leftType): array {
    return [];
  }
}
