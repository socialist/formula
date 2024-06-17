<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;

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

  public static function getTypeCompatibleOperands(IntegerType|FloatType $self, ImplementableOperator $operator): array {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ADDITION:
      case ImplementableOperator::TYPE_SUBTRACTION:
      case ImplementableOperator::TYPE_MULTIPLICATION:
      case ImplementableOperator::TYPE_DIVISION:
      case ImplementableOperator::TYPE_LESS:
      case ImplementableOperator::TYPE_GREATER:
      case ImplementableOperator::TYPE_EQUALS:
        return [new IntegerType(false),new FloatType(false)];
      case ImplementableOperator::TYPE_MODULO:
        return [new IntegerType(false)];
      case ImplementableOperator::TYPE_TYPE_CAST:
        if($self instanceof IntegerType) {
          return [new TypeType(new FloatType(false), false)];
        } else {
          return [new TypeType(new IntegerType(false), false)];
        }
      default:
        return [];
    }
  }

  public static function getTypeOperatorResultType(IntegerType|FloatType $typeA, ImplementableOperator $operator, ?Type $typeB): ?Type {
    // unary operations
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_UNARY_MINUS:
      case ImplementableOperator::TYPE_UNARY_PLUS:
        return $typeA;
    }
    // binary operations
    if($typeB === null) {
      return null;
    }
    if($operator->getID() === ImplementableOperator::TYPE_TYPE_CAST) {
      if($typeB instanceof TypeType) {
        if($typeB->getType() instanceof FloatType) {
          return new FloatType(false);
        }
        if($typeB->getType() instanceof IntegerType) {
          return new IntegerType(false);
        }
      }
      return null;
    }
    // number operations
    if(!($typeB instanceof IntegerType) && !($typeB instanceof FloatType)) {
      return null;
    }
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ADDITION:
      case ImplementableOperator::TYPE_SUBTRACTION:
      case ImplementableOperator::TYPE_MULTIPLICATION:
        return self::getMostPreciseNumberType($typeA, $typeB);
      case ImplementableOperator::TYPE_DIVISION:
        return new FloatType(false);
      case ImplementableOperator::TYPE_MODULO:
        return new IntegerType(false);
      case ImplementableOperator::TYPE_GREATER:
      case ImplementableOperator::TYPE_LESS:
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanType(false);
      default:
        return null;
    }
  }

  public static function numberOperate(IntegerValue|FloatValue $self, ImplementableOperator $operator, ?Value $other): Value {
    // unary operations
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_UNARY_MINUS:
        if($self instanceof FloatValue) {
          return new FloatValue(-$self->toPHPValue());
        } else {
          return new IntegerValue(-$self->toPHPValue());
        }
      case ImplementableOperator::TYPE_UNARY_PLUS:
        return $self; // do nothing
    }
    // binary operations
    if($other === null) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    // cast
    if($operator->getID() === ImplementableOperator::TYPE_TYPE_CAST) {
      if($other instanceof TypeValue) {
        if($other->getValue() instanceof FloatType) {
          return new FloatValue($self->toPHPValue());
        }
        if($other->getValue() instanceof IntegerType) {
          return new IntegerValue((int) $self->toPHPValue());
        }
      }
      throw new \BadFunctionCallException('Invalid operation');
    }
    if(!($other instanceof FloatValue) && !($other instanceof IntegerValue)) { // only numbers
      throw new \BadFunctionCallException('Invalid operation');
    }
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ADDITION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->toPHPValue() + $other->toPHPValue());
      case ImplementableOperator::TYPE_SUBTRACTION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->toPHPValue() - $other->toPHPValue());
      case ImplementableOperator::TYPE_MULTIPLICATION:
        return new (static::getMostPreciseNumberValueClass($self, $other))($self->toPHPValue() * $other->toPHPValue());
      case ImplementableOperator::TYPE_DIVISION:
        return new FloatValue($self->toPHPValue() / $other->toPHPValue());
      case ImplementableOperator::TYPE_MODULO:
        return new IntegerValue($self->toPHPValue() % $other->toPHPValue());
      case ImplementableOperator::TYPE_GREATER:
        return new BooleanValue($self->toPHPValue() > $other->toPHPValue());
      case ImplementableOperator::TYPE_LESS:
        return new BooleanValue($self->toPHPValue() < $other->toPHPValue());
      case ImplementableOperator::TYPE_EQUALS:
        return new BooleanValue($self->toPHPValue() == $other->toPHPValue());
      default:
        throw new \BadFunctionCallException('Invalid operation '.$self->getType()->getIdentifier().' '.$operator->toString(PrettyPrintOptions::buildDefault()).' '.($other !== null ? $other->getType()->getIdentifier() : ''));
    }
  }
}
