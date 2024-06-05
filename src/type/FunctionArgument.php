<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * @author Timo Lehnertz
 */
class FunctionArgument {

  public readonly Type $type;

  public readonly bool $optional;

  public function __construct(Type $type, bool $optional) {
    $this->type = $type;
    $this->optional = $optional;
  }

  public function equals(FunctionArgument $other): bool {
    return $this->type->equals($other->type) && $this->optional === $other->optional;
  }
}
