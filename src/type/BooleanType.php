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

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof BooleanType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'bool';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new BooleanValue(false))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new BooleanValue(false))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'BooleanType'];
  }
}
