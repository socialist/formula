<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\procedure\ValueContainer;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class FieldValue implements ValueContainer {

  private Value $value;

  public function __construct(Value $value) {
    $this->value = $value;
    $this->value->setContainer($this);
  }

  public function getValue(): Value {
    return $this->value;
  }

  public function assign(Value $value): void {
    $this->value->setContainer(null);
    $this->value = $value;
    $this->value->setContainer($this);
  }
}
