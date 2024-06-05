<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 */
class MixedType implements Type {

  public function getIdentifier(bool $nested = false): string {
    return 'mixed';
  }

  public function equals(Type $type): bool {
    return $type instanceof MixedType;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    throw new FormulaValidationException('Mixed does not have any operators');
  }

  public function buildNode(): array {
    return ['type' => 'MixedType'];
  }
}
