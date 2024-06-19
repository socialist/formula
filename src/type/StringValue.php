<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\classes\ClassInstanceValue;
use TimoLehnertz\formula\type\classes\FieldValue;

/**
 * @author Timo Lehnertz
 */
class StringValue extends ClassInstanceValue {

  private readonly string $value;

  public function __construct(string $value) {
    $lengthField = new FieldValue(new IntegerValue(strlen($value)));
    parent::__construct(['length' => $lengthField]);
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
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ADDITION:
        if($other !== null && $other instanceof StringValue) {
          return new StringValue($this->value.$other->toString());
        }
    }
    return parent::valueOperate($operator, $other);
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toString(): string {
    return $this->value;
  }
}
