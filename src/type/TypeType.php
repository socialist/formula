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

  public function equals(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'Type';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }
}
