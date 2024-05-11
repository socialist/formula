<?php
namespace TimoLehnertz\formula\type;

class BooleanValue implements Value {

  private bool $value;

  public function __construct(bool $value) {
    $this->value = $value;
  }

  public function toString(): string {
    return $this->value ? 'true' : 'false';
  }

  public function assign(Value $value): void {
    if($value instanceof BooleanValue) {
      $this->value = $value->value;
    }
    throw new \BadFunctionCallException('Incompatible type');
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

  // used for testing
  public function getValue(): bool {
    return $this->value;
  }
}

