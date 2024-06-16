<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class StringValue extends Value {

  private readonly string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }

  public function copy(): Value {
    return new StringValue($this->value);
  }

  public function isTruthy(): bool {
    return true;
  }

  public function valueEquals(Value $other): bool {
    return $other->value === $this->getValue();
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($other === null || $operator->getID() !== ImplementableOperator::TYPE_ADDITION) {
      throw new InternalFormulaException('Invalid operation on string value!');
    }
    return new StringValue($this->value.$other->toStringValue()->value);
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toStringValue(): StringValue {
    return $this;
  }
}
