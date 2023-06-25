<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\expression\Variable;

class Scope {

  /**
   * 
   * @var Variable[]
   */
  private array $variables = [];
  
  private array $methods = [];
  
  public function __construct() {
    
  }
  
  public function clone(): Scope {
    return clone $this;
  }
  
  public function defineVariable(string $identifier, Type $type): void {
    foreach ($this->variables as $variable) {
      if($variable->getIdentifier() === $identifier) {
        throw new DoublicateVariableException('Redeclaration of variable '.$identifier);
      }
    }
    $variable = new Variable($identifier, $type);
    $this->variables[] = $variable;
  }
}

