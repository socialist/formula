<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\overloads\Addition;
use TimoLehnertz\formula\operator\overloads\Subtraction;
use TimoLehnertz\formula\operator\overloads\UnaryPlus;
use TimoLehnertz\formula\operator\overloads\UnaryMinus;
use TimoLehnertz\formula\operator\overloads\TypeCast;

class FloatValue implements Value, Addition, Subtraction, UnaryPlus, UnaryMinus {

  private float $value;

  public function __construct(float $value) {
    $this->value = $value;
  }

  public function toString(): string {
    return ''.$this->value;
  }

  public function assign(FloatValue $value): void {
    $this->value = $value->value;
  }

  public function getType(): Type {
    return new FloatType();
  }

  public function isTruthy(): bool {
    return $this->value !== 0;
  }

  public function getAdditionResultType(Type $type): ?Type {
    if($type instanceof IntegerType) {
      return new FloatType();
    } else if($type instanceof FloatType) {
      return new FloatType();
    }
  }

  public function operatorAddition(Value $b): Value {
    if($b instanceof FloatValue) {
      return new FloatValue($this->value + $b->value);
    } else if($b instanceof FloatValue) {
      return new FloatValue($this->value + $b->value);
    } else {
      throw new \BadFunctionCallException('Invlid value type');
    }
  }

  public function getSubtractionResultType(Type $type): ?Type {
    if($type instanceof IntegerType) {
      return new FloatType();
    } else if($type instanceof FloatType) {
      return new FloatType();
    }
  }

  public function operatorSubtraction(Value $b): Value {
    if($b instanceof FloatValue) {
      return new FloatValue($this->value - $b->value);
    } else if($b instanceof FloatValue) {
      return new FloatValue($this->value - $b->value);
    } else {
      throw new \BadFunctionCallException('Invlid value type');
    }
  }

  public function getUnaryPlusResultType(Type $type): ?Type {
    return new FloatType();
  }

  public function operatorUnaryPlus(): Value {
    return new FloatValue($this->value);
  }

  public function getUnaryMinusResultType(Type $type): ?Type {
    return new FloatType();
  }

  public function operatorUnaryMinus(): Value {
    return new FloatValue(-$this->value);
  }
}
