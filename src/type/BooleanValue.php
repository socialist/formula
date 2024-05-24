<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class BooleanValue extends Value {

  private bool $value;

  public function __construct(bool $value) {
    $this->value = $value;
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

  public function getValue(): bool {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    return $other->value === $this->value;
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === ImplementableOperator::TYPE_LOGICAL_NOT) {
      return new BooleanValue(!$this->value);
    }
    if($otherType === null || !($otherType instanceof BooleanType)) {
      return null;
    }
    switch($operator->id) {
      case ImplementableOperator::TYPE_LOGICAL_AND:
      case ImplementableOperator::TYPE_LOGICAL_OR:
      case ImplementableOperator::TYPE_LOGICAL_XOR:
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanType();
      default:
        return null;
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->id === ImplementableOperator::TYPE_LOGICAL_NOT) {
      return new BooleanValue(!$this->value);
    }
    if($other === null || !($other instanceof BooleanValue)) {
      throw new \BadFunctionCallException('Invalid value');
    }
    switch($operator->id) {
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return new BooleanValue($other->value && $this->value);
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return new BooleanValue($other->value || $this->value);
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($other->value xor $this->value);
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($other->value xor $this->value);
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanValue($other->value === $this->value);
      default:
        throw new \BadFunctionCallException('Invalid operation');
    }
  }

  public function assign(Value $value): void {
    $this->value = $value->getValue();
  }

  public function toString(PrettyPrintOptions $prettyprintOptions): string {
    return $this->value ? 'true' : 'false';
  }
}
