<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class FloatValue implements Value {

  private float $value;

  public function __construct(float $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->value;
  }

  public function getType(): Type {
    return new FloatType();
  }

  public function isTruthy(): bool {
    return $this->value !== 0;
  }

  public function copy(): FloatValue {
    return new FloatValue($this->value);
  }

  public function getValue(): float {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    return IntegerValue::numberValueEquals($this, $other);
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    return IntegerValue::getNumberOperatorResultType($this, $operator, $otherType);
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    return IntegerValue::numberOperate($this, $operator, $other);
  }
}
