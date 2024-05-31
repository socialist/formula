<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\InternalFormulaException;

/**
 * @author Timo Lehnertz
 */
class NullValue extends Value {

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

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new InternalFormulaException('Invalid operation on null!');
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return 'null';
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNode(): array {
    return ['type' => 'NullValue'];
  }
}
