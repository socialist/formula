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
interface FormulaPart {

  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public function toString(PrettyPrintOptions $prettyPrintOptions): string;
}

