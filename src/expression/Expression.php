<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Locator;
use TimoLehnertz\formula\type\Type;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign if its returntype is not void.
 * Expressions don't define anything in their scopes
 * 
 * @author Timo Lehnertz
 */
abstract class Expression extends FormulaPart {

  /**
   * Run this Expression and all neccessary sub expressions
   * @return Locator
   */
  public abstract function run(): Locator;
}

