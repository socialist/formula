<?php
namespace TimoLehnertz\formula\operator\overloads;

use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\Type;

interface Addition {

  /**
   * Returns the type that will be the result of this operator given the input type.
   * Returns null if theis this type is not allowed
   */
  public function getAdditionResultType(Type $type): ?Type;

  public function operatorAddition(Value $b): Value;
}

