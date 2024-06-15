<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\FormulaBugException;

/**
 * @author Timo Lehnertz
 */
abstract class Value implements OperatorHandler {

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    $array = $this->getValueExpectedOperands($operator);
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        $array[] = $this->getType();
        break;
      case ImplementableOperator::TYPE_EQUALS:
        $array[] = $this->getType();
        break;
      case ImplementableOperator::TYPE_TYPE_CAST:
        foreach($array as $type) {
          if(!($type instanceof TypeType)) {
            throw new FormulaBugException('Cast operator has to expect TypeType');
          }
        }
        $array[] = new TypeType(new BooleanType());
        $array[] = new TypeType($this->getType());
        $array[] = new TypeType(new StringType());
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return [new BooleanType()];
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return [new BooleanType()];
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return [new BooleanType()];
    }
    return $array;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    $type = $this->getValueOperatorResultType($operator, $otherType);
    if($type !== null) {
      return $type;
    }
    // default operators
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        if($otherType === null || !$this->getType()->equals($otherType)) {
          return null;
        }
        return $this->getType();
      case ImplementableOperator::TYPE_EQUALS:
        if($otherType === null || !$this->getType()->equals($otherType)) {
          return null;
        }
        return new BooleanType();
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($otherType instanceof TypeType) {
          if($otherType->getType() instanceof BooleanType) {
            return new BooleanType();
          }
          if($otherType->getType()->equals($this->getType())) {
            return $this->getType();
          }
          if($otherType->getType()->equals(new StringType())) {
            return new StringType();
          }
        }
        return null;
      case ImplementableOperator::TYPE_LOGICAL_AND:
      case ImplementableOperator::TYPE_LOGICAL_OR:
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        if($otherType !== null) {
          return new BooleanType();
        }
    }
    return null;
  }

  public function operate(ImplementableOperator $operator, ?Value $other): Value {
    // default operators
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
        $this->assign($other);
        return $this->copy();
        break;
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        $return = $this->copy();
        $this->assign($other);
        return $return;
        break;
      case ImplementableOperator::TYPE_EQUALS:
        if($this->getType()->equals($other->getType())) {
          return new BooleanValue($this->valueEquals($other));
        }
        break;
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($other instanceof TypeValue) {
          if($other->getValue() instanceof BooleanType) {
            return new BooleanValue($this->isTruthy());
          }
          if($other->getValue()->equals($this->getType())) {
            return $this;
          }
          if($other->getValue()->equals(new StringType())) {
            return $this->toStringValue();
          }
        }
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return new BooleanValue($this->isTruthy() && $other->isTruthy());
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return new BooleanValue($this->isTruthy() || $other->isTruthy());
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($this->isTruthy() xor $other->isTruthy());
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanValue($this->isTruthy() === $other->isTruthy());
    }
    return $this->valueOperate($operator, $other);
  }

  /**
   * @param Value $value guaranteed to be assignable
   */
  public abstract function assign(Value $value): void;

  public abstract function getType(): Type;

  /**
   * Everything should be truthy except for false and nullish values
   */
  public abstract function isTruthy(): bool;

  public abstract function copy(): Value;

  public abstract function toString(PrettyPrintOptions $prettyPrintOptions): string;

  protected abstract function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type;

  protected abstract function valueOperate(ImplementableOperator $operator, ?Value $other): Value;

  /**
   * Returns the expected types for the operator or an empty array if the operator doesnt exist
   * @param ImplementableOperator $operator
   * @return array<Type>
   */
  protected abstract function getValueExpectedOperands(ImplementableOperator $operator): array;

  /**
   * @param Value $other guaranteed to be assignable
   */
  protected abstract function valueEquals(Value $other): bool;

  public abstract function buildNode(): array;

  /**
   * @return mixed the php representation of this Value
   */
  public abstract function toPHPValue(): mixed;

  public abstract function toStringValue(): StringValue;
}
