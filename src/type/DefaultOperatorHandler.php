<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\TypeCastOperator;

/**
 * @author Timo Lehnertz
 */
class DefaultOperatorHandler implements OperatorHandler {

  private readonly Value $value;

  public function __construct(Value $value) {
    $this->value = $value;
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    $array = [];
    switch($operator->id) {
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
        $array[] = $this->value->getType();
        break;
      case Operator::IMPLEMENTABLE_EQUALS:
        $array[] = $this->value->getType();
        break;
        return new BooleanType();
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        $array[] = new BooleanType();
    }
    return array_merge($array, $this->value->getCompatibleOperands($operator));
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): Type {
    // default operators
    switch($operator->id) {
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
        if($otherType === null || !$this->getType()->assignableBy($otherType)) {
          return null;
        }
        return $this->getType();
      case Operator::IMPLEMENTABLE_EQUALS:
        if($otherType === null || !$this->getType()->assignableBy($otherType)) {
          return null;
        }
        return new BooleanType();
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        if($operator instanceof TypeCastOperator) {
          if($operator->getCastType() instanceof BooleanType) {
            return new BooleanType();
          }
        }
      // else default to implemented operators
      default:
        return $this->value->getValueOperatorResultType($operator, $otherType);
    }
  }

  public function operate(ImplementableOperator $operator, ?Value $other): Value {
    // default operators
    switch($operator->id) {
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
        if($other === null || !$this->getType()->assignableBy($other->getType())) {
          throw new InternalFormulaException('assignment had invalid type');
        }
        $this->assign($other);
        return $this;
      case Operator::IMPLEMENTABLE_EQUALS:
        if($other === null || !$this->getType()->assignableBy($other->getType())) {
          throw new InternalFormulaException('can\'t compare '.$this->getType()->getIdentifier().' and '.$other->getType()->getIdentifier());
        }
        return new BooleanValue($this->valueEquals($other));
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        if($operator instanceof TypeCastOperator) {
          if($operator->getCastType() instanceof BooleanType) {
            return new BooleanValue($this->isTruthy());
          }
        }
      // else default to implemented operators
      default:
        return $this->value->valueOperate($operator, $other);
    }
  }
}
