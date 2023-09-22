<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\type\Locator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\FormulaRuntimeException;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class Scope {

  /**
   * Variables will be defined in validation stage in the same order code will be executed
   * after validation stage variables will get undefined to get defined again at runtime
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
  
  /**
   * Array containing all types defined in this scope. Key beeing the type name
   * Doesn't hold parent scopes types
   * @var Type[]
   */
  private array $types;
  
  private bool $topLevel;
  
  public function __construct(bool $topLevel) {
    $this->topLevel = $topLevel;
    if($topLevel) {
      $this->initDefaultTypes();
    }
  }
  
  /**
   * Will initiate the inbuild types.
   * SHOULD only be called for the top level scope
   */
  public function initDefaultTypes(): void {
    $this->types['bool'] = new BooleanType(false);
    $this->types['int'] = new IntegerType(false);
    /**
     * @todo
     */
  }
  
  /**
   * Will be called after validation stage to prepare for runtime
   * Will also be called after each run in a code block
   */
  public function undefineVariables(): void {
    $this->variables = [];
  }
  
  public function getChild(): Scope {
    $child = new Scope(false);
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

  public function defineVariable(Type $type, string $identifier): void {
    if(isset($this->variables[$identifier])) {
      throw new DoublicateVariableException('Redeclaration of variable "'.$identifier.'"');
    }
    $variable = new Variable($identifier, $type->buildNewLocator());
    $this->variables[$identifier] = $variable;
  }
  
  public function initializeVariable(string $identifier, Locator $locator): void {
    if(isset($this->variables[$identifier])) {
      $this->variables[$identifier]->getLocator()->assign($locator);
    } else if($this->parent !== null) {
      $this->parent->initializeVariable($identifier, $locator);
    } else {
      throw new FormulaRuntimeException('Variable "'.$identifier.'" is not defined');
    }
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
  
  public function getTypeByName(string $typeName): Type {
    if(isset($this->types[$typeName])) {
      return $this->types[$typeName];
    } else {
      if($this->parent === null) {
        throw new FormulaValidationException('Type "'.$typeName.'" is not defined');
      } else {
        return $this->parent->getTypeByName($typeName);
      }
    }
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

