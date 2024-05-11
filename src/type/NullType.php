<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class NullType implements Type {

  public function __construct() {}

  public function canCastTo(Type $type): bool {
    return false;
  }

  /**
   * @return SubProperty[]
   */
  public function getSubProperties(): array {
    return [];
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'null';
  }

  public function getImplementedOperators(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this;
  }
}
