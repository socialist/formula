<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
class StringValue extends Value {

  private string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }

  public function getType(): Type {
    return new StringType();
  }

  public function copy(): Value {
    return new StringValue($this->value);
  }

  public function isTruthy(): bool {
    return true;
  }

  public function getValue(): string {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    return $other->value === $this->getValue();
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    if($operator->getID() === Operator::IMPLEMENTABLE_ADDITION) {
      return [new StringType()];
    }
    return [];
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === Operator::IMPLEMENTABLE_ADDITION && $otherType instanceof StringType) {
      return new StringType();
    }
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($other === null || !($other instanceof StringValue) || $operator->getID() !== Operator::IMPLEMENTABLE_ADDITION) {
      throw new InternalFormulaException('Invalid operation on string value!');
    }
    return new StringType($this->value + $other->getValue());
  }

  public function assign(Value $value): void {
    $this->value = $value->getValue();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return "'".$this->value."'";
  }

  public function buildNode(): array {
    return ['type' => 'StringValue','value' => $this->value];
  }

  public function toPHPValue(): mixed {
    return $this->value;
  }

  public function toStringValue(): StringValue {
    return $this;
  }
}
