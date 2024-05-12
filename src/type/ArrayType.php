<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\OperatableOperator;
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

  public function assignableBy(Type $type): bool {
    if(!($type instanceof ArrayType)) {
      return false;
    }
    return $type->keyType->assignableBy($this->keyType) && $this->elementsType->assignableBy($type->elementsType);
  }

  public function getIdentifier(bool $isNested = false): string {
    if($this->keyType instanceof IntegerType) {
      return $this->elementsType->getIdentifier(true).'[]';
    } else {
      return 'array<'.$this->keyType->getIdentifier().','.$this->elementsType->getIdentifier().' >';
    }
  }

  public function validate(Scope $scope): Type {
    return new ArrayType($this->keyType->validate($scope), $this->elementsType->validate($scope));
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    $arrayValue = new ArrayValue([], $this);
    return $arrayValue->getOperatorResultType($operator, $otherType);
  }
}
