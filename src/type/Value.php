<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\ValueContainer;

/**
 * @author Timo Lehnertz
 */
abstract class Value implements OperatorHandler {

  private ?ValueContainer $container = null;

  public function operate(ImplementableOperator $operator, ?Value $other): Value {
    // default operators
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
        if($this->container === null) {
          throw new FormulaBugException('Missing value container');
        }
        $this->container->assign($other);
        return $other;
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        if($this->container === null) {
          throw new FormulaBugException('Missing value container');
        }
        $return = $this->copy();
        $this->container->assign($other);
        return $return;
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanValue($this->valueEquals($other));
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($other instanceof TypeValue) {
          if($other->getValue() instanceof BooleanType) {
            return new BooleanValue($this->isTruthy());
          }
          if($other->getValue()->equals(new StringType(false))) {
            return new StringValue($this->toString());
          }
        }
        break;
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return new BooleanValue($this->isTruthy() && $other->isTruthy());
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return new BooleanValue($this->isTruthy() || $other->isTruthy());
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return new BooleanValue($this->isTruthy() xor $other->isTruthy());
    }
    return $this->valueOperate($operator, $other);
  }

  public function setContainer(?ValueContainer $container): void {
    $this->container = $container;
  }

  /**
   * Everything should be truthy except for false and nullish values
   */
  public abstract function isTruthy(): bool;

  public abstract function copy(): Value;

  protected abstract function valueOperate(ImplementableOperator $operator, ?Value $other): Value;

  /**
   * @param Value $other guaranteed to be assignable
   */
  protected abstract function valueEquals(Value $other): bool;

  /**
   * @return mixed the php representation of this Value
   */
  public abstract function toPHPValue(): mixed;

  public abstract function toString(): string;
}
