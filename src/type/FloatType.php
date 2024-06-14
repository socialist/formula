<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class FloatType implements Type {

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof FloatType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'float';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new FloatValue(0))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new FloatValue(0))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'FloatType'];
  }
}
