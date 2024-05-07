<?php
namespace TimoLehnertz\formula\operator\overloads;

use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\Type;

interface TypeCast {

  public function canCastTo(Type $type): bool;

  public function castTo(Type $type): Value;
}

