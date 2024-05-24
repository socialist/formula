<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign if its returntype is non void.
 * Expressions don't define anything in their scopes
 *
 * @author Timo Lehnertz
 */
interface Expression extends FormulaPart {

  /**
   * Must validate this and all contained Parts.
   * Throws exceptions in case of invalidation.
   *
   * @return Type the implied return type of this expression
   */
  public function validate(Scope $scope): Type;

  public function run(): Value;
}
