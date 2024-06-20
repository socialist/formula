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

  public function copy(): Value {
    return new BooleanValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof BooleanValue && $other->value === $this->value;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->getID() === ImplementableOperator::TYPE_LOGICAL_NOT) {
      return new BooleanValue(!$this->value);
    } else {
      throw new FormulaBugException('Invalid operation');
    }
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toString(): string {
    return $this->value ? 'true' : 'false';
  }
}
