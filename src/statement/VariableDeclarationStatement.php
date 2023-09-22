<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\type\Locator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\StatementReturnInfo;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class VariableDeclarationStatement extends Statement {

  private string $typeName;
  private ?Type $type = null;
  private string $identifier;
  private ?Expression $initilizer;
  
  public function __construct(string $typeName, string $identifier, ?Expression $initilizer) {
    $this->typeName = $typeName;
    $this->identifier = $identifier;
    $this->initilizer = $initilizer;
  }
  
  public function registerDefines(): void {
    // do nothing only register on run
  }
  
  public function validate(Scope $scope, FormulaSettings $formulaSettings): Type {
    // obtain actual type
    $this->type = $this->scope->getTypeByName($this->typeName);
    if($this->initilizer !== null) {
      $type = $this->initilizer->validate($scope, $formulaSettings);
      if(!$this->type->isAssignableWith($type)) {
        throw new \Exception("Can't initialize variable ${$this->identifier} of type ${$this->type->toString()} with type ${$type->toString()}");
      }
    }
    $scope->defineVariable($this->type, $this->identifier);
    return $this->type;
  }

  public function run(Scope $scope): StatementReturnInfo {
    $scope->defineVariable($this->type, $this->identifier);
    if($this->initilizer !== null) {
      $scope->initializeVariable($this->identifier, $this->initilizer->run());
    }
    return StatementReturnInfo::buildBoring();
  }

  public function getSubParts(): array {
    if($this->initilizer === null) {      
      return [];
    } else {
      return [$this->initilizer];
    }
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
