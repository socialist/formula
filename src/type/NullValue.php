<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class NullValue extends Value {

  public function copy(): NullValue {
    return new NullValue(); // immutable anyway
  }

  public function isTruthy(): bool {
    return false;
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof NullValue;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new InternalFormulaException('Invalid operation on null!');
  }

  public function toPHPValue(): mixed {
    return null;
  }

  public function toString(): string {
    return 'null';
  }
}
