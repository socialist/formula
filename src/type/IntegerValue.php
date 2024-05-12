<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class IntegerValue implements Value {

  private int $value;

  public function __construct(int $value) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->value;
  }

  public function assign(Value $value): void {
    if($value instanceof IntegerValue) {
      $this->value = $value->value;
    }
    throw new \BadFunctionCallException('Incompatible type');
  }

  public function getType(): Type {
    return new IntegerType();
  }

  public function copy(): IntegerValue {
    return new IntegerValue($this->value);
  }

  public function isTruthy(): bool {
    return $this->value !== 0;
  }

  public function getValue(): int {
    return $this->value;
  }

  public static function getMostPreciseNumberType(Type $a, Type $b): Type {
    if($b instanceof FloatType) {
      return $b;
    }
    return $a;
  }

  /**
   * @return class-string<IntegerValue|FloatValue>
   */
  public static function getMostPreciseNumberValueClass(IntegerValue|FloatValue $self, IntegerValue|FloatValue $other): string {
    if($self instanceof FloatValue || $other instanceof FloatValue) {
      return FloatValue::class;
    } else {
      return IntegerValue::class;
    }
  }

  public static function numberValueEquals(IntegerType|FloatType $self, Value $other): ?Type {
    if($other instanceof FloatValue || $other instanceof IntegerValue) {
      return $self->getValue() == $other->getValue();
    }
    return false;
  }

  public static function getNumberOperatorResultType(IntegerType|FloatType $typeA, OperatableOperator $operator, ?Type $typeB): ?Type {
    // unary operations
    switch($operator->id) {
      case OperatableOperator::TYPE_UNARY_MINUS:
      case OperatableOperator::TYPE_UNARY_PLUS:
        return $typeA;
    }
    // binary operations
    if($typeB === null || !($typeB instanceof IntegerType) || !($typeB instanceof IntegerType)) {
      return null;
    }
    switch($operator->id) {
      case OperatableOperator::TYPE_ADDITION:
      case OperatableOperator::TYPE_SUBTRACION:
      case OperatableOperator::TYPE_MULTIPLICATION:
      case OperatableOperator::TYPE_DIVISION:
        return self::getMostPreciseNumberType($typeA, $typeB);
      case OperatableOperator::TYPE_GREATER:
      case OperatableOperator::TYPE_LESS:
      case OperatableOperator::TYPE_EQUALS:
        return new BooleanType();
      default:
        return null;
    }
  }

  public static function numberOperate(IntegerValue|FloatValue $self, OperatableOperator $operator, ?Value $other): Value {
    // unary operations
    switch($operator->id) {
      case OperatableOperator::TYPE_UNARY_MINUS:
        return new IntegerValue(-$self->getValue());
      case OperatableOperator::TYPE_UNARY_PLUS:
        return new IntegerValue($self->getValue());
    }
    // binary operations
    if($other === null || (!($other instanceof FloatValue) && !($other instanceof IntegerValue))) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    switch($operator->id) {
      case OperatableOperator::TYPE_ADDITION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() + $other->getValue());
      case OperatableOperator::TYPE_SUBTRACION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() - $other->getValue());
      case OperatableOperator::TYPE_MULTIPLICATION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() * $other->getValue());
      case OperatableOperator::TYPE_DIVISION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() / $other->getValue());
      case OperatableOperator::TYPE_GREATER:
        return new BooleanValue($self->getValue() > $other->getValue());
      case OperatableOperator::TYPE_LESS:
        return new BooleanValue($self->getValue() < $other->getValue());
      case OperatableOperator::TYPE_EQUALS:
        return new BooleanValue($self->getValue() == $other->getValue());
      default:
        throw new \BadFunctionCallException('Invalid operation');
    }
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    return IntegerValue::getNumberOperatorResultType($this, $operator, $otherType);
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    return IntegerValue::numberOperate($this, $operator, $other);
  }

  public function valueEquals(Value $other): bool {
    return IntegerValue::numberValueEquals($this, $other);
  }
}
