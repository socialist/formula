<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use SebastianBergmann\Type\VoidType;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class VoidValue implements Value {

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return 'void';
  }

  public function assign(VoidValue $value): void {}

  public function getType(): Type {
    return new VoidType();
  }

  public function isTruthy(): bool {
    return false;
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    throw new \BadFunctionCallException('Invalid operation');
  }

  public function copy(): Value {
    return $this;
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof VoidValue;
  }
}
