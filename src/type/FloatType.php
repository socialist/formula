<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\OperatableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class FloatType implements Type {

  public function assignableBy(Type $type): bool {
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

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    return (new FloatValue(0))->getOperatorResultType($operator, $otherType);
  }
}
