<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\FormulaBugException;

/**
 * @author Timo Lehnertz
 */
class BooleanValue extends Value {

  private bool $value;

  public function __construct(bool $value) {
    $this->value = $value;
  }

  public function getType(): Type {
    return new BooleanType();
  }

  public function isTruthy(): bool {
    return $this->value;
  }

  public function copy(): BooleanValue {
    return new BooleanValue($this->value);
  }

  public function getValue(): bool {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    return $other->value === $this->value;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operation');
  }

  public function assign(Value $value): void {
    $this->value = $value->getValue();
  }

  public function toString(PrettyPrintOptions $prettyprintOptions): string {
    return $this->value ? 'true' : 'false';
  }

  public function buildNode(): array {
    return ['type' => 'BooleanValue','value' => $this->value];
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toStringValue(): StringValue {
    return new StringValue($this->value ? 'true' : 'false');
  }
}
