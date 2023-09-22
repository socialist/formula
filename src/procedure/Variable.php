<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\type\Locator;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class Variable {
  
  private string $identifier;
  
  private Locator $locator;
  
  public function __construct(string $identifier, Locator $locator) {
    $this->identifier = $identifier;
    $this->locator = $locator;
  }
  
  public function getItentifier(): string {
    return $this->identifier;
  }
  
  public function getLocator(): Locator {
    return $this->locator;
  }
}

