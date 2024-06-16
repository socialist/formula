<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class IntegerValue extends Value {

  private readonly int $value;

  public function __construct(int $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->value;
  }

  public function copy(): IntegerValue {
    return new IntegerValue($this->value);
  }

  public function isTruthy(): bool {
    return $this->value !== 0;
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof IntegerValue) {
      return $other->toPHPValue() === $this->toPHPValue();
    } else if($other instanceof FloatValue) {
      return $other->toPHPValue() == $this->toPHPValue();
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

  public function toStringValue(): StringValue {
    return new StringValue(''.$this->value);
  }
}
