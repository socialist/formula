<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\types\Type;

class Scope {

  /**
   * @var Variable[]
   */
  private array $variables = [];
  
  /**
   * @var Method[]
   */
  private array $methods = [];
  
  private ?Scope $parent = null;
  
  public function __construct() {
    
  }
  
  public function getChild(): Scope {
    $child = clone $this;
    $child->parent = $this;
    return $child;
  }
  
  public function defineMethod(Method $method): void {
    if(isset($this->methods[$method->getIdentifier()])) {
      throw new DoublicateMethodException('Redeclaration of method '.$method->getIdentifier());
    }
    $this->methods[$method->getIdentifier()] = $method;
  }

  public function defineVariable(Variable $variable): void {
    if(isset($this->variables[$variable->getIdentifier()])) {
      throw new DoublicateVariableException('Redeclaration of variable '.$variable->getIdentifier);
    }
    $this->variables[$variable->getIdentifier()] = $variable;
  }
  
  public function getMethod(string $identifier): ?Method {
    if(!isset($this->methods[$identifier])) return null;
    return $this->methods[$identifier];
  }

  public function getVariable(string $identifier): ?Variable {
    if(!isset($this->variables[$identifier])) return null;
    return $this->variables[$identifier];
  }
  
  public function unsetVariable(string $identifier): void {
    unset($this->variables[$identifier]);
  }

  public function unsetMethod(string $identifier): void {
    unset($this->methods[$identifier]);
  }
}

