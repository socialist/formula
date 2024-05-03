<?php
namespace TimoLehnertz\formula\type;

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
}

