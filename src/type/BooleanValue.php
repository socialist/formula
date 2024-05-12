<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class BooleanValue implements Value {

  private bool $value;

  public function __construct(bool $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyprintOptions): string {
    return $this->value ? 'true' : 'false';
  }

  public function getType(): Type {
    return new BooleanType();
  }

  public function isTruthy(): bool {
    return $this->value;
  }

  public function copy(): BooleanValue {
    return new BooleanValue($this->value);
  }

  // used for testing
  public function getValue(): bool {
    return $this->value;
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === OperatableOperator::TYPE_LOGICAL_NOT) {
      return new BooleanValue(!$this->value);
    }
    if($otherType === null || !($otherType instanceof BooleanType)) {
      return null;
    }
    switch($operator->id) {
      case OperatableOperator::TYPE_LOGICAL_AND:
      case OperatableOperator::TYPE_LOGICAL_OR:
      case OperatableOperator::TYPE_LOGICAL_XOR:
      case OperatableOperator::TYPE_EQUALS:
        return new BooleanType();
      default:
        return null;
    }
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    if($operator->id === OperatableOperator::TYPE_LOGICAL_NOT) {
      return new BooleanValue(!$this->value);
    }
    if($other === null || !($other instanceof BooleanValue)) {
      throw new \BadFunctionCallException('Invalid value');
    }
    switch($operator->id) {
      case OperatableOperator::TYPE_LOGICAL_AND:
        return new BooleanValue($other->value && $this->value);
      case OperatableOperator::TYPE_LOGICAL_OR:
        return new BooleanValue($other->value || $this->value);
      case OperatableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($other->value xor $this->value);
      case OperatableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($other->value xor $this->value);
      case OperatableOperator::TYPE_EQUALS:
        return new BooleanValue($other->value === $this->value);
      default:
        throw new \BadFunctionCallException('Invalid operation');
    }
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof BooleanValue) {
      return $other->value === $this->value;
    }
    return false;
  }
}
