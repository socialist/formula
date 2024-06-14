<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class VoidType implements Type {

  public function getIdentifier(bool $nested = false): string {
    return 'void';
  }

  public function assignableBy(Type $type): bool {
    return true;
  }

  public function equals(Type $type): bool {
    return $type instanceof VoidType;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new VoidValue())->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new VoidValue())->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'VoidType'];
  }
}
