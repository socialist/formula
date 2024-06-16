<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\ValueContainer;

/**
 * @author Timo Lehnertz
 */
abstract class Type implements OperatorMeta, FormulaPart {

  private ?ValueContainer $container = null;

  public function __construct() {}

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    $array = $this->getTypeCompatibleOperands($operator);
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        if($this->container === null) {
          throw new FormulaValidationException('Can\'t assign final value');
        }
        $array[] = $this;
        break;
      case ImplementableOperator::TYPE_EQUALS:
        $array[] = $this;
        break;
      case ImplementableOperator::TYPE_TYPE_CAST:
        foreach($array as $type) {
          if(!($type instanceof TypeType)) {
            throw new FormulaBugException('Cast operator has to expect TypeType');
          }
        }
        $array[] = new TypeType(new BooleanType(false), false);
        $array[] = new TypeType(new StringType(false), false);
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return [new BooleanType(false)];
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return [new BooleanType(false)];
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return [new BooleanType(false)];
    }
    return $array;
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        if($otherType === null || !$this->assignableBy($otherType)) {
          break;
        }
        return $this;
      case ImplementableOperator::TYPE_EQUALS:
        if($otherType === null || !$this->assignableBy($otherType)) {
          break;
        }
        return new BooleanType(false);
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($otherType instanceof TypeType) {
          if($otherType->getType() instanceof BooleanType) {
            return new BooleanType(false);
          }
          if($otherType->getType()->equals(new StringType(false))) {
            return new StringType(false);
          }
        }
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
      case ImplementableOperator::TYPE_LOGICAL_OR:
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        if($otherType !== null) {
          return new BooleanType(false);
        }
    }
    return $this->getTypeOperatorResultType($operator, $otherType);
  }

  public function setContainer(?ValueContainer $container): void {
    $this->container = $container;
  }

  protected abstract function getTypeCompatibleOperands(ImplementableOperator $operator): array;

  protected abstract function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type;

  /**
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public abstract function getIdentifier(bool $nested = false): string;

  public abstract function equals(Type $type): bool;

  public function assignableBy(Type $type): bool {
    return $this->typeAssignableBy($type);
  }

  protected abstract function typeAssignableBy(Type $type): bool;

  public abstract function buildNode(): array;

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->getIdentifier();
  }
}
