<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
abstract class NumberValueHelper extends Value {

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

  public static function getValueExpectedOperands(IntegerValue|FloatValue $self, ImplementableOperator $operator): array {
    switch($operator->id) {
      case ImplementableOperator::TYPE_ADDITION:
      case ImplementableOperator::TYPE_SUBTRACTION:
      case ImplementableOperator::TYPE_MULTIPLICATION:
      case ImplementableOperator::TYPE_DIVISION:
      case ImplementableOperator::TYPE_GREATER:
      case ImplementableOperator::TYPE_LESS:
        return [$self->getType()];
      default:
        return [];
    }
  }

  public static function getNumberOperatorResultType(IntegerType|FloatType $typeA, ImplementableOperator $operator, ?Type $typeB): ?Type {
    // unary operations
    switch($operator->id) {
      case ImplementableOperator::TYPE_UNARY_MINUS:
      case ImplementableOperator::TYPE_UNARY_PLUS:
        return $typeA;
    }
    // binary operations
    if($typeB === null || !($typeB instanceof IntegerType) || !($typeB instanceof IntegerType)) {
      return null;
    }
    switch($operator->id) {
      case ImplementableOperator::TYPE_ADDITION:
      case ImplementableOperator::TYPE_SUBTRACTION:
      case ImplementableOperator::TYPE_MULTIPLICATION:
      case ImplementableOperator::TYPE_DIVISION:
        return self::getMostPreciseNumberType($typeA, $typeB);
      case ImplementableOperator::TYPE_GREATER:
      case ImplementableOperator::TYPE_LESS:
        return new BooleanType();
      default:
        return null;
    }
  }

  public static function numberOperate(IntegerValue|FloatValue $self, ImplementableOperator $operator, ?Value $other): Value {
    // unary operations
    switch($operator->id) {
      case ImplementableOperator::TYPE_UNARY_MINUS:
        return new IntegerValue(-$self->getValue());
      case ImplementableOperator::TYPE_UNARY_PLUS:
        return new IntegerValue($self->getValue());
    }
    // binary operations
    if($other === null || (!($other instanceof FloatValue) && !($other instanceof IntegerValue))) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    switch($operator->id) {
      case ImplementableOperator::TYPE_ADDITION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() + $other->getValue());
      case ImplementableOperator::TYPE_SUBTRACTION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() - $other->getValue());
      case ImplementableOperator::TYPE_MULTIPLICATION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() * $other->getValue());
      case ImplementableOperator::TYPE_DIVISION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() / $other->getValue());
      case ImplementableOperator::TYPE_GREATER:
        return new BooleanValue($self->getValue() > $other->getValue());
      case ImplementableOperator::TYPE_LESS:
        return new BooleanValue($self->getValue() < $other->getValue());
      default:
        throw new \BadFunctionCallException('Invalid operation '.$self->getType()->getIdentifier().' '.$operator->toString(PrettyPrintOptions::buildDefault()).' '.$other?->getType()->getIdentifier());
    }
  }
}
