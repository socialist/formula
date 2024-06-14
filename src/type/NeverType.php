<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class NeverType implements Type {

  public function __construct() {}

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof NeverType;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'never';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('NeverType can not be converted to node');
  }
}