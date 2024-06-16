<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class BooleanValue extends Value {

  private readonly bool $value;

  public function __construct(bool $value) {
    $this->value = $value;
  }

  public function isTruthy(): bool {
    return $this->value;
  }

  public function copy(): BooleanValue {
    return new BooleanValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    return $other->value === $this->value;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation');
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toStringValue(): StringValue {
    return new StringValue($this->value ? 'true' : 'false');
  }
}
