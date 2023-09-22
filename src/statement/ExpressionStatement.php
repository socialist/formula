<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\StatementReturnInfo;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\type\Type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class ExpressionStatement extends Statement {

  private Expression $expression;
  
  public function __construct(Expression $expression) {
    $this->expression = $expression;
  }
  
  public function registerDefines() {
    // do nothing
  }

  public function run(Scope $scope): StatementReturnInfo {
    $this->expression->run($scope);
    return StatementReturnInfo::buildBoring();
  }

  public function validate(Scope $scope, FormulaSettings $formulaSettings): Type {
    return $this->expression->validate($formulaSettings);
  }

  public function getSubParts(): array {
    return [$this->expression];
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }
}

