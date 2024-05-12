<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\OperatableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * Type metadata
 * Must be immutable
 *
 * @author Timo Lehnertz
 */
interface Type {

  /**
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public function getIdentifier(bool $nested = false): string;

  public function assignableBy(Type $type): bool;

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type;

  /**
   * Validates this type and returns either this type or create a new one
   */
  public function validate(Scope $scope): Type;
}
