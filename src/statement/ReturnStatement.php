<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\StatementReturnInfo;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\type\Locator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\VoidType;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class ReturnStatement extends Statement {
  
  private ?Expression $returnExpression;
  
  public function __construct(?Expression $returnStatement) {
    $this->returnExpression = $returnStatement;
  }
  
  public function registerDefines() {
    // do nothing
  }
  
  public function run(Scope $scope): StatementReturnInfo {
    if($this->returnExpression === null) {
      $locator = new Locator(new VoidType());
    } else {
      $locator = $this->returnExpression->run($scope);
    }
    return StatementReturnInfo::buildReturn($locator);
  }
  
  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->returnExpression === null) {
      return 'return;';
    } else {
      return 'return '.$this->returnExpression->toString($prettyPrintOptions).';';
    }
  }
  
  public function getSubParts(): array {
    if($this->returnExpression === null) {
      return [];
    } else {
      return $this->returnExpression->getSubExpressions();
    }
  }
  
  public function validate(FormulaSettings $formulaSettings): Type {
    if($this->returnStatement === null) {
      return new VoidType();
    } else {
      return $this->returnStatement->validate($formulaSettings);
    }
  }
}

