<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\NodesNotSupportedException;

/**
 * @author Timo Lehnertz
 */
class ClassTypeType extends Type {

  private readonly ConstructorType $constructorType;

  public function __construct(ConstructorType $constructorType) {
    $this->constructorType = $constructorType;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    throw new NodesNotSupportedException('ClassTypeType');
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function getIdentifier(bool $nested = false): string {
    return 'ClassTypeType';
  }

  public function equals(Type $type): bool {
    if($type instanceof ClassTypeType) {
      return $this->constructorType->equals($type->constructorType);
    }
    return false;
  }

  protected function typeAssignableBy(Type $type): bool {
    return false;
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_NEW:
        return $this->constructorType;
    }
    return null;
  }
}
