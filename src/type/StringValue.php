<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use PHPUnit\Framework\Constraint\Operator;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class StringValue implements Value {

  private string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return "'".$this->value."'";
  }

  public function getType(): Type {
    return new StringType();
  }

  public function copy(): Value {
    return new StringValue($this->value);
  }

  public function isTruthy(): bool {
    return false;
  }

  public function getValue(): string {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    if($other instanceof StringValue) {
      return $other->value === $this->value;
    }
    return false;
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === OperatableOperator::TYPE_ADDITION && $otherType instanceof StringType) {
      return new StringType();
    }
    return null;
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    if($other === null || !($other instanceof StringValue) || $operator->id !== OperatableOperator::TYPE_ADDITION) {
      throw new \BadFunctionCallException('Invalid operation!');
    }
    return new StringType($this->value + $other->value);
  }
}
