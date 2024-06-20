<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class TypeValue extends Value {

  private readonly Type $value;

  public function __construct(Type $value) {
    $this->value = $value;
  }

  public function getValue(): Type {
    return $this->value;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): Value {
    return new TypeValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof TypeValue) {
      return $other->value->equals($this->value);
    } else {
      return false;
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toString(): string {
    return 'TypeValue('.$this->value->getIdentifier().')';
  }
}
