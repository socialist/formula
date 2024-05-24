<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
abstract class PostfixOperator implements Operator {
  use OperatorHelper;

  public function getOperatorType(): OperatorType {
    return OperatorType::PostfixOperator;
  }

  public function validateOperation(?Type $leftType, ?Type $rightType): Type {
    if($rightType !== null) {
      throw new \UnexpectedValueException('Expected leftExpression to be null');
    }
    if($leftType === null) {
      throw new FormulaValidationException('Missing left operand!');
    }
    return $this->validatePostfixOperation($leftType);
  }

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    if($leftValue === null) {
      throw new FormulaValidationException('Missing left operand!');
    }
    return $this->operatePostfix($leftValue);
  }

  protected abstract function validatePostfixOperation(Type $leftType): Type;

  protected abstract function operatePostfix(Value $leftValue): Value;
}
