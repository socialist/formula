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
    if($this->value->y > 0) {
      $format .= $this->value->y.'Y';
    }
    if($this->value->m > 0) {
      $format .= $this->value->m.'M';
    }
    if($this->value->d > 0) {
      $format .= $this->value->d.'D';
    }
    if($this->value->h > 0 || $this->value->i > 0 || $this->value->s > 0) {
      $format .= 'T';
      if($this->value->h > 0) {
        $format .= $this->value->h.'H';
      }
      if($this->value->i > 0) {
        $format .= $this->value->i.'M';
      }
      if($this->value->s > 0) {
        $format .= $this->value->s.'S';
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

  public function copy(): Value {
    return new DateIntervalValue($this->value);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation');
  }

  public function isTruthy(): bool {
    return true;
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof DateIntervalValue) {
      return $other->toString() === $this->toString();
    }
    return false;
  }
}
