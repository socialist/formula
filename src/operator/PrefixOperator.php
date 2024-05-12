<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
abstract class PrefixOperator extends Operator {

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    if($rightValue === null) {
      throw new FormulaValidationException('Missing right operand!');
    }
    return $this->operatePrefix($rightValue);
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

  protected abstract function validatePrefixOperation(Type $rightType): Type;

  protected abstract function operatePrefix(Value $rightValue): Value;
}
