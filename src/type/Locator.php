<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaRuntimeException;

/**
 * Locator class used to locate everything that can be accessed during runtime
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Locator {
  
  private Type $type;
  
  /**
   * Trust that value is of tyoe type
   */
  public function __construct(Type $type) {
    $this->type = $type;
  }
  
  public function assign(Locator $value): void {
    if($this->type->isAssignableWith($value->getType())) {
      $this->setValue($value);
    } else {
      throw new FormulaRuntimeException($this->type::class.' can not be asigned with '.$value);
    }
  }

  protected abstract function setValue(Locator $value): void;
  
  public abstract function copy(): Locator;

  public function getType(): Type {
    return $this->type;
  }
}

