<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 *
 *         Represents a function argument as seen from outside of that function
 */
class OuterFunctionArgument {

  public readonly Type $type;

  public readonly bool $optional;

  public function __construct(Type $type, bool $optional) {
    $this->type = $type;
    $this->optional = $optional;
  }

  public function equals(OuterFunctionArgument $other): bool {
    return $this->type->equals($other->type) && $this->optional === $other->optional;
  }
}
