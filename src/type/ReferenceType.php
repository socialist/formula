<?php
namespace TimoLehnertz\formula\type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ReferenceType implements Type {

  private readonly string $referenceIdentifier;

  public function __construct(string $referenceIdentifier) {
    $this->referenceIdentifier = $referenceIdentifier;
  }

  public function canCastTo(Type $type): bool {
    throw new \BadMethodCallException('ReferenceType must be validated first');
  }

  public function getImplementedOperators(): array {
    throw new \BadMethodCallException('ReferenceType must be validated first');
  }

  public function getSubProperties(): array {
    throw new \BadMethodCallException('ReferenceType must be validated first');
  }

  public function getIdentifier(bool $nested = false): string {
    return $this->referenceIdentifier;
  }
}

