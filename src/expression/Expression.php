<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\procedure\Locator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;
use src\PrettyPrintOptions;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign.
 * Expressions can not define anything in their scope
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Expression {
  
  protected Scope $scope;
  
  public function __construct(Scope $scope) {
    $this->scope = $scope;
  }
  
  /**
   * Will be called after registerDefines(). Should validate this and all sub expressions.
   * Throws exceptions in case of invalidation.
   * Finds and returns the implicit return type of this expression
   */
  public abstract function validate(FormulaSettings $formulaSettings): Type;
  
  public abstract function run(): Locator;
  
  public abstract function getSubExpressions(): array;

  /**
   * Converts this expression back to code
   * @return string
   */
  public abstract function toString(?PrettyPrintOptions $prettyPrintOptions): string;
}

