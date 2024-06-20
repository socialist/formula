<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class FunctionType extends Type {

  public readonly OuterFunctionArgumentListType $arguments;

  public readonly Type $generalReturnType;

  /**
   * @var ?callable(OuterFunctionArgumentListType): ?Type
   */
  private readonly mixed $specificReturnType;

  /**
   * @param ?callable(OuterFunctionArgumentListType): ?Type $specificReturnType
   */
  public function __construct(OuterFunctionArgumentListType $arguments, Type $generalReturnType, ?callable $specificReturnType = null) {
    parent::__construct();
    $this->arguments = $arguments;
    $this->generalReturnType = $generalReturnType;
    $this->specificReturnType = $specificReturnType;
  }

  protected function typeAssignableBy(Type $type): bool {
    if(!($type instanceof FunctionType)) {
      return false;
    }
    return $this->arguments->assignableBy($type->arguments, true) && $this->generalReturnType->assignableBy($type->generalReturnType, true);
  }

  public function equals(Type $type): bool {
    if($type instanceof FunctionType) {
      return $this->arguments->equals($type->arguments) && $this->generalReturnType->equals($type->generalReturnType) && $this->specificReturnType == $type->specificReturnType;
    } else {
      return false;
    }
  }

  public function getIdentifier(bool $nested = false): string {
    return 'function'.$this->arguments->getIdentifier().' -> '.$this->generalReturnType->getIdentifier();
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
      if($otherType instanceof OuterFunctionArgumentListType && $this->arguments->assignableBy($otherType)) {
        if($this->specificReturnType === null) {
          return $this->generalReturnType;
        } else {
          return call_user_func($this->specificReturnType, $otherType);
        }
      }
    }
    return null;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('function', ['arguments' => $this->arguments->buildNodeInterfaceType(),'returnType' => $this->generalReturnType->buildNodeInterfaceType()]);
  }
}
