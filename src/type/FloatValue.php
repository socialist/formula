<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class FloatValue extends Value {

  private readonly float $value;

  public function __construct(float $value) {
    $this->value = $value;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): FloatValue {
    return new FloatValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof IntegerValue) {
      return $other->getValue() == $this->getValue();
    } else if($other instanceof FloatValue) {
      return $other->getValue() === $this->getValue();
    } else {
      return false;
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    return NumberValueHelper::numberOperate($this, $operator, $other);
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toString(): string {
    if(floor($this->value) == $this->value) {
      return ((string) $this->value).'.0';
    } else {
      return (string) $this->value;
    }
  }
}
