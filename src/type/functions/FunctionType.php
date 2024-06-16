<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class FunctionType extends Type {

  public readonly OuterFunctionArgumentListType $arguments;

  public readonly Type $returnType;

  public function __construct(OuterFunctionArgumentListType $arguments, Type $returnType) {
    parent::__construct();
    $this->arguments = $arguments;
    $this->returnType = $returnType;
  }

  protected function typeAssignableBy(Type $type): bool {
    if(!($type instanceof FunctionType)) {
      return false;
    }
    return $this->arguments->assignableBy($type->arguments, true) && $this->returnType->assignableBy($type->returnType, true);
  }

  public function equals(Type $type): bool {
    if($type instanceof FunctionType) {
      return $this->arguments->equals($type->arguments) && $this->returnType->equals($type->returnType);
    } else {
      return false;
    }
  }

  public function getIdentifier(bool $nested = false): string {
    return $this->arguments->getIdentifier().': '.$this->returnType->getIdentifier();
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    if($operator->getID() === ImplementableOperator::TYPE_CALL) {
      return [$this->arguments];
    } else {
      return [];
    }
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === ImplementableOperator::TYPE_CALL) {
      return $this->returnType;
    } else {
      return [];
    }
  }

  public function buildNode(): array {
    return ['type' => 'FunctionType','signature' => $this->getIdentifier()];
  }
}
