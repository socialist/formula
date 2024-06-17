<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class FloatType extends Type {

  public function __construct() {
    parent::__construct();
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof FloatType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'float';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return NumberValueHelper::getTypeCompatibleOperands($this, $operator);
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return NumberValueHelper::getTypeOperatorResultType($this, $operator, $otherType);
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('float');
  }
}
