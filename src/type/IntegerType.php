<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class IntegerType implements Type {

  public function equals(Type $type): bool {
    return $type instanceof IntegerType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'int';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new IntegerValue(0))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new IntegerValue(0))->getCompatibleOperands($operator);
  }
}
