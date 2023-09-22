<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * Superclass for all parsed components of a formula program
 * 
 * @author Timo Lehnertz
 *
 */
abstract class FormulaPart {
  
  protected Scope $scope;
  
  /**
   * MUST set the scope of this part. MUST also set the scope for all sub parts
   * 
   * Will be called first
   */
  public abstract function setScope(Scope $scope);
  
  /**
   * MUST register all defines inside this part in the correct scope
   * 
   * will get called second
   */
  public abstract function registerDefines(): void;
  
  /**
   * Will be called after registerDefines(). Must validate this and all sub expressions.
   * Throws exceptions in case of invalidation.
   * @return Type the implied return type of this expression
   * 
   * will get called thrird
   */
  public abstract function validate(FormulaSettings $formulaSettings): Type;
  
  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public abstract function toString(?PrettyPrintOptions $prettyPrintOptions): string;
  
  /**
   * @return array that holds all Parts that make up this one
   */
  public abstract function getSubParts(): array;
}

