<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class BooleanType extends Type {

  public function __construct() {
    parent::__construct();
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof BooleanType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'boolean';
  }

  public function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNode(): array {
    return ['type' => 'BooleanType'];
  }
}
