<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\overloads\Addition;

class StringValue implements Value, Addition {

  private string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }

  public function toString(): string {
    return "'".$this->value."'";
  }

  public function assign(Value $value): void {
    if($value instanceof StringValue) {
      $this->value = $value->value;
    }
    throw new \BadFunctionCallException('Incompatible type');
  }

  public function getType(): Type {
    return new StringType();
  }

  public function canCastTo(Type $type): bool {
    return false;
  }

  public function castTo(Type $type): Value {
    throw new \BadFunctionCallException('Invalid cast');
  }

  public function getAdditionResultType(Type $type): ?Type {
    if($type instanceof StringType) {
      return new StringType();
    }
    return null;
  }

  public function copy(): Value {
    return new StringValue($this->value);
  }

  public function isTruthy(): bool {
    return false;
  }

  // used for testing
  public function getValue(): string {
    return $this->value;
  }

  public function operatorAddition(Value $b): Value {
    if($b instanceof StringValue) {
      return new StringValue($this->value + $b->value);
    } else {
      throw new \BadFunctionCallException('Invlid value type');
    }
  }
}

