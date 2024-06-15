<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaRuntimeException extends FormulaPartException {

  public function __construc(FormulaPart $formulaPart, string $message) {
    parent::__construct($formulaPart, 'Runtime error', $message);
  }
}
