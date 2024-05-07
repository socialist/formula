<?php
namespace src\type;

use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use SebastianBergmann\Type\VoidType;

class VoidValue implements Value {

  public function toString(): string {
    return 'void';
  }

  public function assign(VoidValue $value): void {}

  public function getType(): Type {
    return new VoidType();
  }

  public function isTruthy(): bool {
    return false;
  }
}

