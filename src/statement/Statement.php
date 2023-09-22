<?php
namespace TimoLehnertz\formula\src\statement;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\StatementReturnInfo;
use TimoLehnertz\formula\type\Type;

/**
 * A statement is an executable piece of code.
 * E.g. a loop, class, ff statement, assignment or similar
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Statement extends FormulaPart {
  
  protected Scope $scope;
  
  /**
   * MUST be called before calling to validate()
   * MUST register all defines using the private scope
   * MUST register all defines inside subParts
   */
  public abstract function registerDefines(): void;
  
  /**
   * registerDefines MUST be called first. SHOULD validate this and all sub expressions
   * MUST Throw an exception in case of invalidation
   * Finds and returns the implied return type of this expression
   */
  public abstract function validate(Scope $scope, FormulaSettings $formulaSettings): Type;
  
  /**
   * Run this Expression and all neccessary sub expressions
   * @return StatementReturnInfo the container for returned info
   */
  public abstract function run(): StatementReturnInfo;
}

