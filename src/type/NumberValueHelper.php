<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
abstract class NumberValueHelper {

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
    switch($operator->getID()) {
      case Operator::IMPLEMENTABLE_ADDITION:
      case Operator::IMPLEMENTABLE_SUBTRACTION:
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
      case Operator::IMPLEMENTABLE_DIVISION:
      case Operator::IMPLEMENTABLE_LESS:
      case Operator::IMPLEMENTABLE_GREATER:
      case Operator::IMPLEMENTABLE_EQUALS:
        return [new IntegerType(),new FloatType()];
      case Operator::IMPLEMENTABLE_MODULO:
        return [new IntegerType()];
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        if($self instanceof IntegerValue) {
          return [new TypeType(new FloatType())];
        } else {
          return [new TypeType(new IntegerType())];
        }
      default:
        return [];
    }
  }

  public static function getNumberOperatorResultType(IntegerType|FloatType $typeA, ImplementableOperator $operator, ?Type $typeB): ?Type {
    // unary operations
    switch($operator->getID()) {
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
        return $typeA;
    }
    // binary operations
    if($typeB === null) {
      return null;
    }
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST) {
      if($typeB instanceof TypeType) {
        if($typeB->getType() instanceof FloatType) {
          return new FloatType();
        }
        if($typeB->getType() instanceof IntegerType) {
          return new IntegerType();
        }
      }
      return null;
    }
    // number operations
    if(!($typeB instanceof IntegerType) && !($typeB instanceof FloatType)) {
      return null;
    }
    switch($operator->getID()) {
      case Operator::IMPLEMENTABLE_ADDITION:
      case Operator::IMPLEMENTABLE_SUBTRACTION:
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
        return self::getMostPreciseNumberType($typeA, $typeB);
      case Operator::IMPLEMENTABLE_DIVISION:
        return new FloatType();
      case Operator::IMPLEMENTABLE_MODULO:
        return new IntegerType();
      case Operator::IMPLEMENTABLE_GREATER:
      case Operator::IMPLEMENTABLE_LESS:
      case Operator::IMPLEMENTABLE_EQUALS:
        return new BooleanType();
      default:
        return null;
    }
  }

  public static function numberOperate(IntegerValue|FloatValue $self, ImplementableOperator $operator, ?Value $other): Value {
    // unary operations
    switch($operator->getID()) {
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
        return new IntegerValue(-$self->getValue());
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
        return new IntegerValue($self->getValue());
    }
    // binary operations
    if($other === null) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    // cast
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST) {
      if($other instanceof TypeValue) {
        if($other->getValue() instanceof FloatType) {
          return new FloatValue($self->getValue());
        }
        if($other->getValue() instanceof IntegerType) {
          return new IntegerValue((int) $self->getValue());
        }
      }
      throw new \BadFunctionCallException('Invalid operation');
    }
    if(!($other instanceof FloatValue) && !($other instanceof IntegerValue)) { // only numbers
      throw new \BadFunctionCallException('Invalid operation');
    }
    switch($operator->getID()) {
      case Operator::IMPLEMENTABLE_ADDITION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() + $other->getValue());
      case Operator::IMPLEMENTABLE_SUBTRACTION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() - $other->getValue());
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->getValue() * $other->getValue());
      case Operator::IMPLEMENTABLE_DIVISION:
        return new FloatValue($self->getValue() / $other->getValue());
      case Operator::IMPLEMENTABLE_MODULO:
        return new IntegerValue($self->getValue() % $other->getValue());
      case Operator::IMPLEMENTABLE_GREATER:
        return new BooleanValue($self->getValue() > $other->getValue());
      case Operator::IMPLEMENTABLE_LESS:
        return new BooleanValue($self->getValue() < $other->getValue());
      case Operator::IMPLEMENTABLE_EQUALS:
        return new BooleanValue($self->getValue() == $other->getValue());
      default:
        throw new \BadFunctionCallException('Invalid operation '.$self->getType()->getIdentifier().' '.$operator->toString(PrettyPrintOptions::buildDefault()).' '.$other?->getType()->getIdentifier());
    }
  }
}
