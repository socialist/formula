<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class StringType extends Type {

  public function __construct() {
    parent::__construct();
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'string';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    if($operator->getID() === ImplementableOperator::TYPE_ADDITION) {
      return [new StringType(false)];
    } else {
      return [];
    }
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === ImplementableOperator::TYPE_ADDITION && $otherType instanceof StringType) {
      return new StringType(false);
    } else {
      return null;
    }
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('String');
  }
}
