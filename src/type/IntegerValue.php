<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\overloads\Addition;
use TimoLehnertz\formula\operator\overloads\Subtraction;
use TimoLehnertz\formula\operator\overloads\UnaryMinus;
use TimoLehnertz\formula\operator\overloads\UnaryPlus;

class IntegerValue implements Value, Addition, Subtraction, UnaryPlus, UnaryMinus {

  private int $value;

  public function __construct(int $value) {
    $this->value = $value;
  }

  public function toString(): string {
    return ''.$this->value;
  }

  public function assign(IntegerValue $value): void {
    $this->value = $value->value;
  }

  public function getType(): Type {
    return new IntegerType();
  }

  public function getAdditionResultType(Type $type): ?Type {
    if($type instanceof IntegerType) {
      return new IntegerType();
    } else if($type instanceof FloatType) {
      return new FloatType();
    }
  }

  public function operatorAddition(Value $b): Value {
    if($b instanceof IntegerValue) {
      return new IntegerType($this->value + $b->value);
    } else if($b instanceof FloatValue) {
      return new FloatValue($this->value + $b->value);
    } else {
      throw new \BadFunctionCallException('Invlid value type');
    }
  }

  public function getSubtractionResultType(Type $type): ?Type {
    if($type instanceof IntegerType) {
      return new IntegerType();
    } else if($type instanceof FloatType) {
      return new FloatType();
    }
  }

  public function operatorSubtraction(Value $b): Value {
    if($b instanceof IntegerValue) {
      return new IntegerType($this->value - $b->value);
    } else if($b instanceof FloatValue) {
      return new FloatValue($this->value - $b->value);
    } else {
      throw new \BadFunctionCallException('Invlid value type');
    }
  }

  public function getUnaryPlusResultType(Type $type): ?Type {
    return new IntegerType();
  }

  public function operatorUnaryPlus(): Value {
    return new IntegerValue($this->value);
  }

  public function getUnaryMinusResultType(Type $type): ?Type {
    return new IntegerType();
  }

  public function operatorUnaryMinus(): Value {
    return new IntegerValue(-$this->value);
  }
}

