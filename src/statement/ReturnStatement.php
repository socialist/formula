<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Locator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\VoidType;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\types\Type;
use src\PrettyPrintOptions;

class ReturnStatement extends Statement {
  
  private ?Expression $returnExpression;
  
  public function __construct(Scope $scope, ?Expression $returnStatement) {
    parent::__construct($scope);
    $this->returnExpression = $returnStatement;
  }
  
  public function registerDefines() {
    // do nothing
  }
  
  public function run(): Locator {
    if($this->returnExpression !== null) {
      return $this->returnExpression->run();
    } else {
      return new Locator(new VoidType(), null);
    }
  }
  
  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->returnExpression !== null) {
      return 'return '.$this->returnExpression->toString($prettyPrintOptions).';';
    } else {
      return 'return;';
    }
  }
  
  public function getSubExpressions(): array {
    if($this->returnExpression !== null) {
      return $this->returnExpression->getSubExpressions();
    } else {
      return [];
    }
  }
  
  public function validate(FormulaSettings $formulaSettings): Type {
    if($this->returnStatement !== null) {
      return $this->returnStatement->validate($formulaSettings);
    } else {
      return new VoidType();
    }
  }
}

