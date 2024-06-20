<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use const false;

/**
 * @author Timo Lehnertz
 */
class DateIntervalType extends Type {

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof DateIntervalType;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('DateIntervalType');
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function getIdentifier(bool $nested = false): string {
    return 'DateInterval';
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }
}
