<?php
namespace TimoLehnertz\formula;

/**
 * Superclass for all parsed components of a formula program
 *
 * @author Timo Lehnertz
 *
 */
interface FormulaPart extends Identifiable {

  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public function toString(PrettyPrintOptions $prettyPrintOptions): string;
}
