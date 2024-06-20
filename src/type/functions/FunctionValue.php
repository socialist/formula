<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\procedure\Scope;

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

  public function valueEquals(Value $other): bool {
    return $other instanceof FunctionValue && $other->body === $this->body;
  }

  public function copy(): Value {
    return new FunctionValue($this->body);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->getID() === ImplementableOperator::TYPE_CALL && $other !== null && $other instanceof OuterFunctionArgumentListValue) {
      return $this->body->call($other);
    } else {
      throw new FormulaBugException('Invalid operator');
    }
  }

  public function toPHPValue(): mixed {
    $body = $this->body;
    return function (...$args) use (&$body) {
      $values = [];
      foreach($args as $arg) {
        $values[] = Scope::convertPHPVar($arg, true)[1];
      }
      return $body->call(new OuterFunctionArgumentListValue($values))->toPHPValue();
    };
  }

  public function toString(): string {
    return 'function';
  }
}

