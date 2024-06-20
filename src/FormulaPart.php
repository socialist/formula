<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
interface FormulaPart {

  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public function toString(PrettyPrintOptions $prettyPrintOptions): string;
}
