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
    if($other instanceof IntegerValue) {
      return $other->getValue() == $this->getValue();
    } else if($other instanceof FloatValue) {
      return $other->getValue() === $this->getValue();
    } else {
      return false;
    }
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    return NumberValueHelper::getValueExpectedOperands($this, $operator);
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return NumberValueHelper::getNumberOperatorResultType($this->getType(), $operator, $otherType);
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

  public function buildNode(): array {
    return ['type' => 'FloatValue','value' => $this->value];
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toStringValue(): StringValue {
    return new StringValue(''.$this->value);
  }
}
