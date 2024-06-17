<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class FunctionValue extends Value {

  private FunctionBody $body;

  public function __construct(FunctionBody $body) {
    $this->body = $body;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): FunctionValue {
    return new FunctionValue($this->body);
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->getID() === ImplementableOperator::TYPE_CALL && $other !== null && $other instanceof OuterFunctionArgumentListValue) {
      return $this->body->call($other);
    } else {
      throw new FormulaBugException('Invalid operator');
    }
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('FunctionValue list does not have a php representation');
  }

  public function toString(): string {
    return 'function';
  }
}

