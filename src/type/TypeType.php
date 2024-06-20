<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class TypeType extends Type {

  private readonly Type $type;

  public function __construct(Type $type) {
    parent::__construct();
    $this->type = $type;
  }

  public function getType(): Type {
    return $this->type;
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return ($type instanceof TypeType) && $type->type->equals($this->type);
  }

  public function getIdentifier(bool $nested = false): string {
    return 'TypeType('.$this->type->getIdentifier().')';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('TypeType', ['type' => $this->type->buildNodeInterfaceType()]);
  }
}
