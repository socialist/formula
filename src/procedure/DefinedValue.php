<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class DefinedValue {

  public readonly Type $type;

  private ?Value $value = null;

  public function __construct(Type $type) {
    $this->type = $type;
  }

  public function assign(Value $value): void {
    if(!$value->getType()->equals($this->type)) {
      throw new FormulaRuntimeException('Type missmatch');
    }
    if($this->value === null) {
      $this->value = $value;
    } else {
      $this->value->assign($value);
    }
  }

  public function get(): Value {
    if($this->value === null) {
      throw new FormulaRuntimeException('Value has been red before initilization');
    }
    return $this->value;
  }

  public function copy(): DefinedValue {
    $copy = new DefinedValue($this->type);
    $copy->value = $this->value?->copy();
    return $copy;
  }
}
