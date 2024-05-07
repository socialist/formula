<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class BooleanType implements Type {

  public function canCastTo(Type $type): bool {
    return $type instanceof BooleanType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'bool';
  }

  public function getSubProperties(): array {
    return [];
  }

  public function getImplementedOperators(): array {}

  public function validate(Scope $scope): Type {
    return $this;
  }
}

