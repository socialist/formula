<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class TypeValue extends Value {

  private Type $value;

  public function __construct(Type $value) {
    $this->value = $value;
  }

  public function getValue(): Type {
    return $this->value;
  }

  public function getType(): Type {
    throw new \BadFunctionCallException();
  }

  public function isTruthy(): bool {
    throw new \BadFunctionCallException();
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    throw new \BadFunctionCallException();
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    throw new \BadFunctionCallException();
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new \BadFunctionCallException();
  }

  public function assign(Value $value): void {
    throw new \BadFunctionCallException();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    throw new \BadFunctionCallException();
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    throw new \BadFunctionCallException();
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    throw new \BadFunctionCallException();
  }
}

