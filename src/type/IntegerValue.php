<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class IntegerValue extends Value {

  private int $value;

  public function __construct(int $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->value;
  }

  public function assign(Value $value): void {
    $this->value = $value->value;
  }

  public function getType(): Type {
    return new IntegerType();
  }

  public function copy(): IntegerValue {
    return new IntegerValue($this->value);
  }

  public function isTruthy(): bool {
    return $this->value !== 0;
  }

  public function getValue(): int {
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
}
