<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaPartException extends \Exception {

  private readonly FormulaPart $formulaPart;

  public function __construc(FormulaPart $formulaPart, string $message) {
    parent::__construct($message);
    $this->formulaPart = $formulaPart;
  }
}
