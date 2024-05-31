<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * @author Timo Lehnertz
 */
class FunctionArgument {

  public readonly string $identifier;

  public readonly Type $type;

  public readonly bool $optional;

  public function __construct(string $identifier, Type $type, bool $optional) {
    $this->identifier = $identifier;
    $this->type = $type;
    $this->optional = $optional;
  }

  public function equals(FunctionArgument $other): bool {
    return $this->identifier === $other->identifier && $this->type->equals($other->type) && $this->optional === $other->optional;
  }
}
