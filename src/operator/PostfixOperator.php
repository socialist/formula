<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
abstract class PostfixOperator extends Operator {

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    if($leftValue === null) {
      throw new FormulaValidationException('Missing left operand!');
    }
    return $this->operatePostfix($leftValue);
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

  protected abstract function validatePostfixOperation(Type $leftType): Type;

  protected abstract function operatePostfix(Value $leftValue): Value;
}
