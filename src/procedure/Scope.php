<?php
namespace TimoLehnertz\formula\procedure;

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
  
  /**
   * @var Scope[]
   */
  private array $children = [];
  
  public function __construct() {
    
  }
  
  public function getChild(): Scope {
    $child = clone $this;
    $child->parent = $this;
    $this->children[] = $child;
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
    if(isset($this->methods[$identifier])) {
      return $this->methods[$identifier];
    }
    if($this->parent !== null) {      
      return $this->parent->getMethod($identifier);
    }
    return null;
  }

  public function getVariable(string $identifier): ?Variable {
    if(isset($this->variables[$identifier])) {
      return $this->variables[$identifier];
    }
    if($this->parent !== null) {
      $this->parent->getVariable($identifier);
    }
    return null;
  }
  
  /**
   * Dont delete
   * might be useful for user defined functions and variables
   */
//   public function unsetVariable(string $identifier): void {
//     unset($this->variables[$identifier]);
//   }

//   public function unsetMethod(string $identifier): void {
//     unset($this->methods[$identifier]);
//   }
}

