<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class FloatType implements Type {

  public function canCastTo(Type $type): bool {
    return $type instanceof FloatType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'float';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  /**
   *
   * @return SubProperty[]
   */
  public function getSubProperties(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }

  public function castTo(Type $type, Value $value): Value {
    if($type instanceof IntegerType) {
      return new FloatValue($value);
    }
  }
}

