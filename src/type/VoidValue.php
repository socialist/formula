<?php
namespace src\type;

class VoidValue implements Value {

  public function toString(): string {
    return 'void';
  }

  public function assign(VoidValue $value): void {}
}

