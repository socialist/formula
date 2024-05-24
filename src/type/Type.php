<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\procedure\Scope;

/**
 * Type metadata
 * Must be immutable
 *
 * @author Timo Lehnertz
 */
interface Type extends OperatorMeta {

  /**
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public function getIdentifier(bool $nested = false): string;

  /**
   * Validates this type and returns either this type or create a new one
   */
  public function validate(Scope $scope): Type;

  public function equals(Type $type): bool;
}
