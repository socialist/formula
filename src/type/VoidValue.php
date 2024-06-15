<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class VoidValue extends Value {

  public function assign(Value $value): void {}

  public function getType(): Type {
    return new VoidType();
  }

  public function isTruthy(): bool {
    return false;
  }

  public function copy(): Value {
    return $this;
  }

  public function valueEquals(Value $other): bool {
    return $other instanceof VoidValue;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new InternalFormulaException('Invalid operation on void');
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return 'void';
  }

  public function buildNode(): array {
    return ['type' => 'VoidValue'];
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('VoidValue does not have a php representation');
  }

  public function toStringValue(): StringValue {
    return new StringValue('void');
  }
}
