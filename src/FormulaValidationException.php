<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaValidationException extends FormulaPartException {

  public function __construct(FormulaPart $formulaPart, string $message) {
    parent::__construct($formulaPart, 'Validation error', $message);
  }
}
