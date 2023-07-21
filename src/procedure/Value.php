<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\types\Type;

/**
 * Readonly class for handling all values
 *
 * @author Timo Lehnertz
 */
class Value {
  
  private Type $type;

  private $value;

  public function __construct(Type $type, $value) {
    $this->type = $type;
    $this->value = $value;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function getValue() {
    return $this->value;
  }
}

