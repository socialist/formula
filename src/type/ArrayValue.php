<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\operator\ArrayAccessOperator;
use TimoLehnertz\formula\operator\OperatableOperator;

/**
 * @author Timo Lehnertz
 */
class ArrayValue implements Value {

  /**
   * @var array<array-key, Value>
   */
  private array $value;

  private ArrayType $type;

  /**
   * @param array<array-key, Value>
   */
  public function __construct(array $value, ArrayType $arrayType) {
    $this->value = $value;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $del = '';
    /** @var Expression $value */
    foreach($this->value as $value) {
      $string .= $del.$value->toString($prettyPrintOptions);
      $del = ',';
    }
    return '{'.$string.'}';
  }

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return $this->value;
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  // used for testing
  public function getValue(): bool {
    return $this->value;
  }

  public function operate(OperatableOperator $operator, ?Value $other): Value {
    if($operator instanceof ArrayAccessOperator) {
      if($other instanceof IntegerValue) {
        $key = $other->getValue();
      }
      if($other instanceof FloatValue) {
        $key = $other->getValue();
      }
      if($other instanceof StringValue) {
        $key = $other->getValue();
      }
      if(defined($this->value[$key])) {
        return $this->value[$key];
      } else {
        throw new \BadFunctionCallException('Array key does not exist!');
      }
    } else if($operator->id === OperatableOperator::TYPE_EQUALS) {
      return new BooleanValue($this === $other);
    } else {
      throw new \BadFunctionCallException('Invalid operator!');
    }
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    if($operator instanceof ArrayAccessOperator) {
      if(!$operator->getIndexType()->canCastTo($this->keyType)) {
        return null;
      }
      return $this->elementsType;
    } else if($operator->id === OperatableOperator::TYPE_EQUALS && $otherType instanceof ArrayType) {
      return new BooleanType();
    }
    return null;
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }
}

