<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\FormulaBugException;

/**
 * @author Timo Lehnertz
 */
class DateIntervalValue extends Value {

  private readonly \DateInterval $value;

  public function __construct(\DateInterval $value) {
    $this->value = $value;
  }

  public function toString(): string {
    $format = 'P';
    if($this->instance->y > 0) {
      $format .= $this->instance->y.'Y';
    }
    if($this->instance->m > 0) {
      $format .= $this->instance->m.'M';
    }
    if($this->instance->d > 0) {
      $format .= $this->instance->d.'D';
    }
    if($this->instance->h > 0 || $this->instance->i > 0 || $this->instance->s > 0) {
      $format .= 'T';
      if($this->instance->h > 0) {
        $format .= $this->instance->h.'H';
      }
      if($this->instance->i > 0) {
        $format .= $this->instance->i.'M';
      }
      if($this->instance->s > 0) {
        $format .= $this->instance->s.'S';
      }
    }
    if($format === 'P') {
      $format .= '0D';
    }
    return $format;
  }

  public function toPHPValue(): \DateInterval {
    return $this->value;
  }

  public function copy(): DateIntervalValue {
    return new DateIntervalValue($this->value);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation');
  }

  public function isTruthy(): bool {
    return true;
  }

  protected function valueEquals(Value $other): bool {
    if($other instanceof DateIntervalValue) {
      return $other->value == $this->value;
    }
    return false;
  }
}
