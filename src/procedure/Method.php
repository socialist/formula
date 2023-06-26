<?php
namespace TimoLehnertz\formula\procedure;

/**
 * Readonly ?
 * 
 * @author Timo Lehnertz
 */
class Method {
  
  private string $identifier;
  
  private callable $callable;
  
  public function __construct(string $identifier, callable $callable) {
    $this->identifier = $identifier;
    $this->callable = $callable;
  }
  
  public function getIdentifier(): string {
    return $this->identifier;
  }
  
  public function getCallable(): callable {
    return $this->callable;
  }
}

