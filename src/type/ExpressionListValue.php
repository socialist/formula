<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class ExpressionListValue extends Value {

  /**
   * @var array<Value>
   */
  private array $values;

  private ExpressionListType $type;

  /**
   * @param array<Value>
   */
  public function __construct(array $values, ExpressionListType $type) {
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
    return new ExpressionListValue($this->values, $this->type);
  }

  public function valueEquals(Value $other): bool {
    if(!($other instanceof ExpressionListValue)) {
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
    throw new \BadMethodCallException('ExpressionListValue cant operate');
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    throw new \BadMethodCallException('ExpressionListValue cant operate');
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
    throw new \BadMethodCallException('ExpressionListValue can build nodes');
  }
}
