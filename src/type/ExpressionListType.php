<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ExpressionListType implements Type {

  /**
   * @var array<Type>
   */
  private array $expressionTypes;

  /**
   * @var array<Type>
   */
  public function __construct(array $expressionTypes) {
    $this->expressionTypes = $expressionTypes;
  }

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    if(!($type instanceof ExpressionListType)) {
      return false;
    }
    if(count($this->expressionTypes) !== count($type->expressionTypes)) {
      return false;
    }
    for($i = 0;$i < count($type->expressionTypes);$i++) {
      if(!$type->expressionTypes[$i]->equals($this->expressionTypes[$i])) {
        return false;
      }
    }
    return true;
  }

  public function getIdentifier(bool $isNested = false): string {
    $identifier = '';
    $delimiter = '';
    foreach($this->expressionTypes as $expressionType) {
      $identifier .= $delimiter.$expressionType->getIdentifier();
      $delimiter = ',';
    }
    return $identifier;
  }

  public function validate(Scope $scope): Type {
    $types = [];
    foreach($this->expressionTypes as $i => $expressionTypes) {
      $types[$i] = $expressionTypes->validate($scope);
    }
    return new ExpressionListType($types);
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    throw new \BadMethodCallException('ExpressionListType cant operate');
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    throw new \BadMethodCallException('ExpressionListType cant operate');
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('ExpressionListType cant operate');
  }
}
