<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class MemberAccsessType extends Type {

  private string $memberIdentifier;

  public function __construct(string $memberIdentifier) {
    parent::__construct();
    $this->memberIdentifier = $memberIdentifier;
  }

  public function getMemberIdentifier(): string {
    return $this->memberIdentifier;
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return ($type instanceof MemberAccsessType) && $type->memberIdentifier === $this->memberIdentifier;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'member accsess('.$this->memberIdentifier.')';
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('MemberAccsessType', ['memberIdentifier' => $this->memberIdentifier]);
  }
}
