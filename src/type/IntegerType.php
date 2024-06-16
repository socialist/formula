<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class IntegerType extends Type {

  public function __construct() {
    parent::__construct();
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof IntegerType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'int';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return NumberValueHelper::getTypeCompatibleOperands($this, $operator);
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return NumberValueHelper::getTypeOperatorResultType($this, $operator, $otherType);
  }

  public function buildNode(): array {
    return ['type' => 'IntegerType'];
  }
}
