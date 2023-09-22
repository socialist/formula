<?php
namespace src\type;

use TimoLehnertz\formula\type\Locator;

class BooleanLocator extends Locator {

  private bool $value = false; // default initial value
  
  protected function setValue(BooleanLocator $value) {
    $this->value = $value->value;
  }

  public function copy(): Locator {
    $booleanLocator = new BooleanLocator($this->getType());
    $booleanLocator->value = $this->value;
    return $booleanLocator;
  }
}

