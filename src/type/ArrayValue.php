<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\classes\ClassInstanceValue;
use TimoLehnertz\formula\type\classes\FieldValue;
use const false;
use const true;

/**
 * @author Timo Lehnertz
 */
class ArrayValue extends ClassInstanceValue implements IteratableValue {

  /**
   * @var array<array-key, Value>
   */
  private array $value;

  private readonly FieldValue $lengthField;

  /**
   * @param array<array-key, Value>
   */
  public function __construct(array $value) {
    $this->lengthField = new FieldValue(new IntegerValue(count($value)));
    parent::__construct(['length' => $this->lengthField]);
    $this->value = $value;
    /** @var Value $value */
    foreach($this->value as $key => $value) {
      $value->setContainer(new ArrayPointerValue($this, $key));
    }
  }

  public function isTruthy(): bool {
    return true;
  }

  public function valueEquals(Value $other, bool $deep = false): bool {
    if(!($other instanceof ArrayValue)) {
      return false;
    }
    if(!$deep) {
      return $other === $this;
    } else {
      if(count($this->value) !== coun($other->value)) {
        return false;
      }
      foreach($this->value as $key => $value) {
        if(!$value->equals($other->value[$key])) {
          return false;
        }
      }
      return true;
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_ARRAY_ACCESS:
        $key = $other->toPHPValue();
        if(isset($this->value[$key])) {
          return $this->value[$key];
        } else {
          return new ArrayPointerValue($this, $key);
        }
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        return parent::valueOperate($operator, $other);
    }
    throw new FormulaBugException('Invalid operation');
  }

  public function assignKey(mixed $key, Value $value): void {
    if(isset($this->value[$key])) {
      $this->value[$key]->setContainer(null);
    }
    $this->value[$key] = $value;
    $this->value[$key]->setContainer(new ArrayPointerValue($this, $key));
    $this->lengthField->assign(new IntegerValue(count($this->value)));
  }

  public function toPHPValue(): mixed {
    $arr = [];
    foreach($this->value as $key => $value) {
      $arr[$key] = $value->toPHPValue();
    }
    return $arr;
  }

  public function toString(): string {
    $str = '{';
    $del = '';
    foreach($this->value as $element) {
      $str .= $del.$element->toString();
      $del = ', ';
    }
    return $str.'}';
  }

  public function getIterator(): \Iterator {
    return new \ArrayIterator($this->value);
  }
}

