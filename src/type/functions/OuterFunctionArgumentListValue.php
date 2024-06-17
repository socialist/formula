<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class OuterFunctionArgumentListValue extends Value {

  /**
   * @var array<Value>
   */
  private readonly array $values;

  /**
   * @param array<Value>
   */
  public function __construct(array $values) {
    $this->values = $values;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new OuterFunctionArgumentListValue($this->values);
  }

  public function valueEquals(Value $other): bool {
    return false;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('OuterFunctionArgumentListValue cant be operated');
  }

  public function getValues(): array {
    return $this->values;
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('OuterFunctionArgumentListValue does not have a php representation');
  }

  public function toString(): string {
    return 'OuterFunctionArgumentListValue';
  }
}
