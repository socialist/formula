<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\type\Type;

/**
 * A Sub property is something that can be accsess from a given type.
 * Examples: type.variable, type.function(), type[3]?
 *
 * @author Timo Lehnertz
 *        
 */
class SubProperty {

  private readonly string $identifier;

  private readonly Type $type;

  public function __construct(string $identifier, Type $type) {
    $this->identifier = $identifier;
    $this->type = $type;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }

  public function getType(): string {
    return $this->type;
  }
}

