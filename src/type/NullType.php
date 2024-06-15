<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class NullType extends Type {

  public function __construct() {
    parent::__construct();
  }

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof NullType;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'null';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new NullValue())->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new NullValue())->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'NullType'];
  }
}
