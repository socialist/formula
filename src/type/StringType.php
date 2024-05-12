<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\OperatableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class StringType implements Type {

  public function assignableBy(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'string';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function castTo(Type $type, Value $value): Value {}

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    return (new StringValue(''))->getOperatorResultType($operator, $otherType);
  }
}
