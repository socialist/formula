<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ReferenceType implements Type {

  private readonly string $referenceIdentifier;

  public function __construct(string $referenceIdentifier) {
    $this->referenceIdentifier = $referenceIdentifier;
  }

  public function equals(Type $type): bool {
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

  public function validate(Scope $scope): Type {
    return $scope->getType($this->referenceIdentifier);
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    throw new \BadFunctionCallException('Must validate first');
  }
}
