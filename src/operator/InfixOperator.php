<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
abstract class InfixOperator implements Operator {
  use OperatorHelper;

  public function getOperatorType(): OperatorType {
    return OperatorType::InfixOperator;
  }

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    if($leftValue === null || $rightValue === null) {
      throw new FormulaValidationException('Missing operand!');
    }
    return $this->operateInfix($leftValue, $rightValue);
  }

  public function validateOperation(?Type $leftType, ?Type $rightType): Type {
    if($leftType === null || $rightType === null) {
      throw new \UnexpectedValueException('Missing operand');
    }
    return $this->validateInfixOperation($leftType, $rightType);
  }

  protected abstract function validateInfixOperation(Type $leftValue, Type $rightType): Type;

  protected abstract function operateInfix(Value $leftValue, Value $rightValue): Value;
}
