<?php
namespace TimoLehnertz\formula\src\statement;

use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * A statement is a executable piece of code that differentiates itself
 * from an Expression by not beeing able to stand right to an equal sign.
 * Also Statements can declare stuff in their scope wich Expressions can't.
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Statement extends Expression {
  
  /**
   * Will be called before call to validate()
   * @param Scope $scope
   */
  public abstract function registerDefines(): void;
}
