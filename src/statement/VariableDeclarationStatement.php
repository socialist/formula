<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\src\statement\Statement;
use src\PrettyPrintOptions;
use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\types\Type;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Locator;

class VariableDeclarationStatement extends Statement {

  private Type $type;
  private string $identifier;
  private ?Expression $initilizer;
  
  public function registerDefines(): void {
    $this->scope->defineVariable($method);
  }

  public function run(): Locator {
    
  }

  public function getSubExpressions(): array {
    if($this->initilizer !== null) {      
      return [$this->initilizer];
    } else {
      return [];
    }
  }

  public function validate(FormulaSettings $formulaSettings): Type {
    if($this->initilizer !== null) {
      $type = $this->initilizer->validate($formulaSettings);
      if(!$this->type->isAssignableWith($type)) {
        throw new \Exception("Can't initialize variable ${$this->identifier} of type ${$this->type->toString()} with type ${$type->toString()}");
      }
    }
    return $this->type;
  }
  
  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $declarationStr = $this->type->toString().' '.$this->identifier;
    $initilizationStr = '';
    if($this->initilizer !== null) {
      return $initilizationStr = '='.$this->initilizer->toString($prettyPrintOptions).';';
    }
    return $declarationStr.$initilizationStr.';';
  }
}
