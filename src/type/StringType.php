<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class StringType implements Type {

  public function equals(Type $type): bool {
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

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new StringValue(''))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new StringValue(''))->getCompatibleOperands($operator);
  }
}
