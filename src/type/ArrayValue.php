<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\operator\ArrayAccessOperator;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
class ArrayValue extends Value {

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

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  public function &getValue(): bool {
    return $this->value;
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {}

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator instanceof ArrayAccessOperator) {
      if(!$operator->getIndexType()->canCastTo($this->keyType)) {
        return null;
      }
      return $this->elementsType;
    } else if($operator->getID() === Operator::IMPLEMENTABLE_EQUALS && $otherType instanceof ArrayType) {
      return new BooleanType();
    }
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
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
    } else if($operator->getID() === Operator::IMPLEMENTABLE_EQUALS) {
      return new BooleanValue($this === $other);
    } else {
      throw new \BadFunctionCallException('Invalid operator!');
    }
  }

  public function assign(Value $value): void {
    $this->value = &$value->getValue();
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

  public function buildNode(): array {
    $elements = [];
    foreach($this->value as $key => $value) {
      $elements[$key] = $value->buildNode();
    }
    return ['type' => 'ArrayValue','elements' => $elements];
  }

  public function toPHPValue(): mixed {
    $arr = [];
    foreach($this->value as $key => $value) {
      $arr[$key] = $value->toPHPValue();
    }
    return $arr;
  }
}

