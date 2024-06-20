<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\classes\ClassType;
use TimoLehnertz\formula\type\classes\FieldType;

/**
 * @author Timo Lehnertz
 */
class StringType extends ClassType {

  public function __construct() {
    $lengthField = new FieldType(true, new IntegerType());
    parent::__construct(null, 'String', ['length' => $lengthField]);
  }

  protected function typeAssignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    return $type instanceof StringType;
  }

  public function getIdentifier(bool $nested = false): string {
    return 'String';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    if($operator->getID() === ImplementableOperator::TYPE_ADDITION) {
      return [new StringType(false)];
    } else {
      return parent::getTypeCompatibleOperands($operator);
    }
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === ImplementableOperator::TYPE_ADDITION && $otherType instanceof StringType) {
      return new StringType(false);
    } else {
      return parent::getTypeOperatorResultType($operator, $otherType);
    }
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('String');
  }
}
