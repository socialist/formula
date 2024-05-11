<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * @author Timo Lehnertz
 */
class NullValue implements Value {

  public function toString(): string {
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

  public function castTo(Type $type): Value {
    throw new \BadFunctionCallException('Invalid cast');
  }

  public function getAdditionResultType(Type $type): ?Type {
    return null;
  }

  public function copy(): NullValue {
    return $this; // immutable anyway
  }

  public function isTruthy(): bool {
    return false;
  }
}
