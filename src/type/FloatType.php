<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class FloatType implements Type {

  public function equals(Type $type): bool {
    return $type instanceof FloatType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'float';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new FloatValue(0))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new FloatValue(0))->getExpectedOperands($operator);
  }
}
