<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class ArrayType extends Type implements IteratableType {

  private Type $keyType;

  private Type $elementsType;

  public function __construct(Type $keyType, Type $elementsType) {
    parent::__construct();
    $this->keyType = $keyType;
    $this->elementsType = $elementsType;
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
    return $type->keyType->equals($this->keyType) && $this->elementsType->equals($type->elementsType);
  }

  public function getIdentifier(bool $isNested = false): string {
    if($this->keyType instanceof IntegerType) {
      return $this->elementsType->getIdentifier(true).'[]';
    } else {
      return 'array<'.$this->keyType->getIdentifier().','.$this->elementsType->getIdentifier().'>';
    }
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if(!$this->keyType->assignableBy($otherType)) {
      return null;
    }
    return $this->elementsType;
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    if($operator->getID() === ImplementableOperator::TYPE_ARRAY_ACCESS) {
      return [$this->keyType];
    }
    return [];
  }

  public function buildNode(): array {
    return ['type' => 'ArrayType','keyType' => $this->keyType->buildNode(),'elementsType' => $this->elementsType->buildNode()];
  }

  public function getElementsType(): Type {
    return $this->elementsType;
  }
}
