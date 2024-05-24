<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class FloatValue extends Value {

  private float $value;

  public function __construct(float $value) {
    $this->value = $value;
  }

  public function getType(): Type {
    return new FloatType();
  }

  public function isTruthy(): bool {
    return true;
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

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    return NumberValueHelper::getValueExpectedOperands($this, $operator);
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return NumberValueHelper::getNumberOperatorResultType($this, $operator, $otherType);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    return NumberValueHelper::numberOperate($this, $operator, $other);
  }

  public function assign(Value $value): void {
    $this->value = $value->value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->value;
  }
}
