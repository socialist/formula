<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class DefinedValue implements ValueContainer {

  private readonly bool $final;

  public readonly Type $type;

  private ?Value $value = null;

  public function __construct(bool $final, Type $type, ?Value $initialValue) {
    $this->final = $final;
    $this->type = $type->setFinal($this->final);
    $this->value = $initialValue;
    $this->value?->setContainer($this->final ? null : $this);
  }

  public function assign(Value $value): void {
    if($this->final) {
      throw new FormulaRuntimeException('Cant mutate immutable value');
    }
    $this->value?->setContainer(null);
    $this->value = $value;
    $this->value->setContainer($this);
  }

  public function get(): Value {
    if($this->value === null) {
      throw new FormulaRuntimeException('Value has been read before initilization');
    }
    return $this->value;
  }
}
