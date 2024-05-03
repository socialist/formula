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

  public function assign(BooleanValue $value): void {
    $this->value = $value->value;
  }

  public function getType(): Type {
    return new BooleanType();
  }
}

