<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class TypeType implements Type {

  private readonly Type $type;

  public function __construct(Type $type) {
    $this->type = $type;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return ($type instanceof TypeType) && $type->getType()->equals($this->type);
  }

  public function getIdentifier(bool $nested = false): string {
    return 'Type';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  public function buildNode(): array {
    return ['type' => 'TypeType','type' => $this->type->buildNode()];
  }
}
