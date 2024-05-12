<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class NullValue implements Value {

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return 'null';
  }

  public function assign(Value $value): void {
    throw new \BadFunctionCallException('invalid assignment');
  }

  public function getType(): Type {
    return new NullType();
  }

  public function canCastTo(Type $type): bool {
    return false;
  }

  public function copy(): NullValue {
    return $this; // immutable anyway
  }

  public function isTruthy(): bool {
    return false;
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof NullValue;
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === OperatableOperator::TYPE_EQUALS && (new NullType())->can) {
      return new BooleanType();
    }
    return null;
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    if($operator->id === OperatableOperator::TYPE_EQUALS) {
      return new BooleanValue($other instanceof NullValue);
    }
    throw new \BadFunctionCallException('Invalid operation!');
  }
}
