<?php
namespace src\operator;

use src\type\Value;
use TimoLehnertz\formula\type\Type;

interface LogicalAnd {

  public function supportsType(Type $type): bool;

  public function logicalAnd(Value $value): Value;
}

