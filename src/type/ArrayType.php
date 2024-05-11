<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ArrayType implements Type {

  private Type $keyType;

  private Type $elementsType;

  public function __construct(Type $keyType, Type $elementsType) {
    $this->keyType = $keyType;
    $this->elementsType = $elementsType;
  }

  public function canCastTo(Type $type): bool {
    if(!($type instanceof ArrayType)) {
      return false;
    }
    return $this->keyType->canCastTo($type->keyType) && $this->elementsType->canCastTo($type->elementsType);
  }

  /**
   * @return SubProperty[]
   */
  public function getSubProperties(): array {
    return [];
  }

  public function getIdentifier(bool $isNested = false): string {
    if($this->keyType instanceof IntegerType) {
      return $this->elementsType->getIdentifier(true).'[]';
    } else {
      return 'array<'.$this->keyType->getIdentifier().','.$this->elementsType->getIdentifier().' >';
    }
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return new ArrayType($this->keyType->validate($scope), $this->elementsType->validate($scope));
  }
}
