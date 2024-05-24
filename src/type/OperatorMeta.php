<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
interface OperatorMeta {

  /**
   * Returns a list of compatible operand types for the given Operator
   * @return array<Type>
   */
  public abstract function getCompatibleOperands(ImplementableOperator $operator): array;

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type;
}
