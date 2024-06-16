<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class ArrayValue extends Value implements IteratableValue {

  /**
   * @var array<array-key, Value>
   */
  private readonly array $value;

  /**
   * @param array<array-key, Value>
   */
  public function __construct(array $value) {
    $this->value = $value;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->getID() === ImplementableOperator::TYPE_ARRAY_ACCESS) {
      if($other instanceof IntegerValue) {
        $key = $other->toPHPValue();
      } else if($other instanceof FloatValue) {
        $key = $other->toPHPValue();
      } else if($other instanceof StringValue) {
        $key = $other->toPHPValue();
      } else {
        throw new FormulaBugException('Invalid operation');
      }
      if(isset($this->value[$key])) {
        return $this->value[$key];
      } else {
        return new ArrayPointerValue($this, $key);
      }
    } else {
      throw new FormulaBugException('Invalid operator!');
    }
  }

  public function assignKey(mixed $key, Value $value): void {
    $this->value[$key] = $value;
  }

  public function toPHPValue(): mixed {
    $arr = [];
    foreach($this->value as $key => $value) {
      $arr[$key] = $value->toPHPValue();
    }
    return $arr;
  }

  public function toStringValue(): StringValue {
    $str = '{';
    $del = '';
    foreach($this->value as $element) {
      $str .= $del.$element->toStringValue()->toPHPValue();
      $del = ', ';
    }
    return new StringValue($str.'}');
  }

  public function getIterator(): \Iterator {
    return new \ArrayIterator($this->value);
  }
}

