<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
abstract class FormulaPart implements Identifiable {

  private readonly int $identificationID;

  private static $identifiableCount = 0;

  public function __construct() {
    $this->identificationID = FormulaPart::$identifiableCount++;
  }

  public function getIdentificationID(): int {
    //     if(!isset($this->identificationID)) {
    //       var_dump($this);
    //     }
    return $this->identificationID;
  }

  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public abstract function toString(PrettyPrintOptions $prettyPrintOptions): string;
}
