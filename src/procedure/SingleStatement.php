<?php
namespace src\procedure;

use TimoLehnertz\formula\expression\FormulaExpression;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;

class SingleStatement implements Statement {

  private FormulaExpression $formulaExpression;
  
  private Scope $scope;
  
  public function __construct(FormulaExpression $formulaExpression) {
    $this->formulaExpression = $formulaExpression;
  }
  
  public function run(): ReturnValue {
//     return $this->formulaExpression->
  }

  public function validate(Scope $scope): Type {
    $this->scope = $scope;
    $this->formulaExpression->validate(false, $this->scope);
  }
  
  public function toString() {
    return $this->formulaExpression->toString();
  }
}

