<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class NeverType extends Type {

  public function __construct() {
    parent::__construct();
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof NeverType;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'never';
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('NeverType can not be converted to node');
  }
}