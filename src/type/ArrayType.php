<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\operator\Operator;

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
      return 'array<'.$this->keyType->getIdentifier().','.$this->elementsType->getIdentifier().' >';
    }
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    $arrayValue = new ArrayValue([], $this);
    return $arrayValue->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    if($operator->getID() === Operator::IMPLEMENTABLE_ARRAY_ACCESS) {
      return [$this->keyType];
    }
    return [];
  }

  public function buildNode(): array {
    return ['type' => 'ArrayType','keyType' => $this->keyType->buildNode(),'elementsType' => $this->elementsType->buildNode()];
  }
}
