<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class MixedType extends Type {

  public function __construct() {
    parent::__construct();
  }

  public function getIdentifier(bool $nested = false): string {
    return 'mixed';
  }

  protected function typeAssignableBy(Type $type): bool {
    return true;
  }

  public function equals(Type $type): bool {
    return $type instanceof MixedType;
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('mixed');
  }
}
