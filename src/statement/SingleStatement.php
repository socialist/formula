<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\expression\FormulaExpression;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\types\Type;

class SingleStatement extends Statement {

  private FormulaExpression $formulaExpression;
  
  public function __construct(FormulaExpression $formulaExpression) {
    $this->formulaExpression = $formulaExpression;
  }
  
  public function run(): ReturnValue {
//     return $this->formulaExpression->
  }

  public function validate(FormulaSettings $formulaSettings): Type {
    $this->formulaExpression->validate(false, $this->scope);
  }
  
  public function toString() {
    return $this->formulaExpression->toString().';';
  }
  
  public function registerDefines(): void {
    // do nothing
  }
  
  public function getSubExpressions(): array {
    return [$this->formulaExpression];
  }
}

