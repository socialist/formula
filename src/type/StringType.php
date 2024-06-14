<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class StringType implements Type {

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'string';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new StringValue(''))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new StringValue(''))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'StringType'];
  }
}
