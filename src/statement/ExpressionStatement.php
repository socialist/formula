<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Locator;
use TimoLehnertz\formula\src\statement\Statement;
use src\PrettyPrintOptions;

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

  public function run(): Locator {
    return $this->expression->run();
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }

  public function getSubExpressions(): array {
    return [$this->expression];
  }

  public function validate(FormulaSettings $formulaSettings): string {
    return $this->expression->validate($formulaSettings);
  }
}

