<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ClassTypeValue extends Value {

  private readonly ConstructorValue $constructor;

  public function __construct(ConstructorValue $constructor) {
    $this->constructor = $constructor;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  public function copy(): ClassTypeValue {
    return new ClassTypeValue($this->constructor);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_NEW:
        return $this->constructor;
    }
    throw new FormulaBugException('Invalid operation');
  }

  public function toPHPValue(): mixed {
    return $this;
  }

  public function toString(): string {
    return 'classType';
  }
}
