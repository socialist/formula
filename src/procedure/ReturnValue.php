<?php
namespace TimoLehnertz\formula\procedure;

class ReturnValue {
  
  private Value $value;
  
  private ?Variable $locator;
  
  public function __construct(Value $value, ?Variable $locator) {
    $this->value = $value;
    $this->locator = $locator;
  }
  
  public function getLocator(): ?Variable {
    return $this->locator;
  }

  public function getValue(): Value {
    return $this->value;
  }
}

