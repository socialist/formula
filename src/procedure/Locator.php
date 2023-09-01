<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\NullpointerException;
use TimoLehnertz\formula\types\Type;

class Locator {
  
  private Type $type;
  
  private string $identifier;
  
  private $value = null;
  
  /**
   * Trust that value is of tyoe type
   */
  public function __construct(Type $type, $value) {
    $this->type = $type;
    $this->value = $value;
  }
  
  public function getType(): Type {
    return $this->type;
  }

  public function getIdentifier(): Type {
    return $this->identifier;
  }
  
  public function setValue($value): void {
    if(!$this->type->isAssignableWith($value)) {
      throw new TypeMissmatchException($this->type::class.' can not be asigned with '.$value);
    }
    $this->value = $value;
  }

  public function getValue() {
    if($this->value === null) throw new NullpointerException('Variable '.$this->identifier.' has not been initialized yet');
    return $this->value;
  }
}

