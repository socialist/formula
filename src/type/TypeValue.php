<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
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
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function isTruthy(): bool {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function assign(Value $value): void {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function buildNode(): array {
    throw new FormulaBugException('Invalid call to TypeValue');
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('TypeValue list does not have a php representation');
  }

  public function toStringValue(): StringValue {
    return new StringValue($this->value->getIdentifier());
  }
}
