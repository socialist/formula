<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\type\Value;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign if its returntype is non void.
 * Expressions don't define anything in their scopes
 *
 * @author Timo Lehnertz
 */
interface Expression extends FormulaPart {

  public function run(): Value;
}
