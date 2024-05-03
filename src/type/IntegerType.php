<?php
namespace TimoLehnertz\formula\type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class IntegerType implements Type {

  public function canCastTo(Type $type): bool {
    return $type instanceof IntegerType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'int';
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

