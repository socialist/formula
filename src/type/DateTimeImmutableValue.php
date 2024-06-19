<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use const false;
use const true;

/**
 * @author Timo Lehnertz
 */
class DateTimeImmutableValue extends Value {

  private readonly \DateTimeImmutable $value;

  public function __construct(\DateTimeImmutable $value) {
    $this->value = $value;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ADDITION:
        if($other instanceof DateIntervalValue) {
          return new DateTimeImmutableValue($this->value->add($other->toPHPValue()));
        }
      case ImplementableOperator::TYPE_SUBTRACTION:
        if($other instanceof DateIntervalValue) {
          return new DateTimeImmutableValue($this->value->sub($other->toPHPValue()));
        }
    }
    return parent::valueOperate($operator, $other);
  }

  public function toString(): string {
    return '"'.$this->value->format('Y-m-d\TH:i:s').'"';
  }

  public function toPHPValue(): \DateTimeImmutable {
    return $this->value;
  }

  public function copy(): DateTimeImmutableValue {
    return new DateTimeImmutableValue($this->value);
  }

  public function isTruthy(): bool {
    return true;
  }

  protected function valueEquals(Value $other): bool {
    if($other instanceof DateTimeImmutableValue) {
      return $this->value == $other->value;
    }
    return false;
  }
}
