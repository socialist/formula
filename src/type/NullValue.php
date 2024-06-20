<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use const false;

/**
 * @author Timo Lehnertz
 */
class NullValue extends Value {

  public function copy(): NullValue {
    return new NullValue();
  }

  public function isTruthy(): bool {
    return false;
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof NullValue;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation on null!');
  }

  public function toPHPValue(): mixed {
    return null;
  }

  public function toString(): string {
    return 'null';
  }
}
