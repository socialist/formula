<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class VoidValue extends Value {

  public function isTruthy(): bool {
    return false;
  }

  public function copy(): Value {
    return new VoidValue();
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof VoidValue;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation on void');
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('VoidValue does not have a php representation');
  }

  public function toString(): string {
    return new StringValue('void');
  }
}
