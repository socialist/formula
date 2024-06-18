<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
abstract class Type implements OperatorMeta, FormulaPart {

  // final per default
  private bool $final = true;

  public function __construct() {}

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    $array = $this->getTypeCompatibleOperands($operator);
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        if($this->final) {
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
        if($this->final) {
          return null;
        }
        if($otherType === null || !$this->assignableBy($otherType)) {
          break;
        }
        return $this->setFinal(true);
      case ImplementableOperator::TYPE_EQUALS:
        if($otherType === null || !$this->assignableBy($otherType)) {
          break;
        }
        return new BooleanType();
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($otherType instanceof TypeType) {
          if($otherType->getType() instanceof BooleanType) {
            return new BooleanType();
          }
          if($otherType->getType()->equals(new StringType())) {
            return new StringType();
          }
        }
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
      case ImplementableOperator::TYPE_LOGICAL_OR:
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        if($otherType !== null) {
          return new BooleanType();
        }
    }
    return $this->getTypeOperatorResultType($operator, $otherType);
  }

  public function setFinal(bool $final): Type {
    $clone = clone $this;
    $clone->final = $final;
    return $clone;
  }

  public function isFinal(): bool {
    return $this->final;
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

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->getIdentifier();
  }

  public abstract function buildNodeInterfaceType(): NodeInterfaceType;
}
