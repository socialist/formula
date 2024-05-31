<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
class ArgumentListValue extends Value {

  /**
   * @var array<Value>
   */
  private readonly array $values;

  private readonly ArgumentListType $type;

  /**
   * @param array<Value>
   */
  public function __construct(array $values, ArgumentListType $type) {
    $this->values = $values;
    $this->type = $type;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new ArgumentListValue($this->values, $this->type);
  }

  public function valueEquals(Value $other): bool {
    if(!($other instanceof ArgumentListValue)) {
      return false;
    }
    if(count($this->values) !== count($other->values)) {
      return false;
    }
    foreach($this->values as $i => $value) {
      if(!$this->values[$i]->equals($value->values[$i])) {
        return false;
      }
    }
    return true;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    if($operator->id === Operator::IMPLEMENTABLE_TYPE_CAST) {
      return [new TypeType(new ArgumentListType(null))];
    } else {
      return [];
    }
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === Operator::IMPLEMENTABLE_TYPE_CAST) {
      if(($otherType instanceof TypeType) && ($otherType->getType() instanceof ArgumentListType)) {}
    }
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new \BadMethodCallException('ExpressionListValue cant operate');
  }

  public function assign(Value $value): void {
    throw new \BadMethodCallException('ExpressionListValue cant operate');
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $del = '';
    /** @var Expression $value */
    foreach($this->value as $value) {
      $string .= $del.$value->toString($prettyPrintOptions);
      $del = ',';
    }
    return $string;
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('ArgumentListValue can build nodes');
  }
}
