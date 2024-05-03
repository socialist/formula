<?php
namespace TimoLehnertz\formula\operator\overloads;

use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\Type;

interface Subtraction {

  /**
   * Returns the type that will be the result of this operator given the input type.
   * Returns null if theis this type is not allowed
   */
  public function getSubtractionResultType(Type $type): ?Type;

  public function operatorSubtraction(Value $b): Value;
}

