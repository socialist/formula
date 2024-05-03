<?php
namespace TimoLehnertz\formula\type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class VoidType implements Type {

  public function getIdentifier(bool $nested = false): string {
    return 'void';
  }

  public function canCastTo(Type $type): bool {
    return $type instanceof VoidType;
  }

  public function getSubProperties(): array {
    return [];
  }

  public function getImplementedOperators(): array {
    return [];
  }
}
