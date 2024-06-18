<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\classes\ClassType;
use TimoLehnertz\formula\type\classes\FieldType;

/**
 * @author Timo Lehnertz
 */
class ArrayType extends ClassType implements IteratableType {

  private Type $keyType;

  private Type $elementsType;

  public function __construct(Type $keyType, Type $elementsType) {
    parent::__construct(null, 'array', ['length' => new FieldType(true, new IntegerType())]);
    $this->keyType = $keyType->setFinal(false);
    $this->elementsType = $elementsType->setFinal(false);
  }

  protected function typeAssignableBy(Type $type): bool {
    if(!($type instanceof ArrayType)) {
      return false;
    }
    $keysCompatible = $this->keyType->assignableBy($type->keyType, true) || ($type->keyType instanceof NeverType);
    $elementsCompatible = $this->elementsType->assignableBy($type->elementsType, true) || ($type->elementsType instanceof NeverType);
    return $keysCompatible && $elementsCompatible;
  }

  public function equals(Type $type): bool {
    if(!($type instanceof ArrayType)) {
      return false;
    }
    return $this->keyType->equals($type->keyType) && $this->elementsType->equals($type->elementsType);
  }

  public function getIdentifier(bool $isNested = false): string {
    if($this->keyType instanceof IntegerType) {
      return $this->elementsType->getIdentifier(true).'[]';
    } else {
      return 'array<'.$this->keyType->getIdentifier().','.$this->elementsType->getIdentifier().'>';
    }
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ARRAY_ACCESS:
        return [$this->keyType];
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        return parent::getTypeCompatibleOperands($operator);
    }
    return [];
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ARRAY_ACCESS:
        if($this->keyType->assignableBy($otherType)) {
          return $this->elementsType;
        }
        break;
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        return parent::getTypeOperatorResultType($operator, $otherType);
    }
    return null;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('array', ['keyType' => $this->keyType->buildNodeInterfaceType(),'elementsType' => $this->elementsType->buildNodeInterfaceType()]);
  }

  public function getKeyType(): Type {
    return $this->keyType;
  }

  public function getElementsType(): Type {
    return $this->elementsType;
  }
}
