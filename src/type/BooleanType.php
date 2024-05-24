<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 *
 */
class BooleanType implements Type {

  public function equals(Type $type): bool {
    return $type instanceof BooleanType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'bool';
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new BooleanValue(false))->getOperatorResultType($operator, $otherType);
  }
}