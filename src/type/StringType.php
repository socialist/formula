<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class StringType implements Type {

  public function canCastTo(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'string';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  /**
   * @return SubProperty[]
   */
  public function getSubProperties(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function castTo(Type $type, Value $value): Value {}
}

