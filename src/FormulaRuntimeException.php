<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaRuntimeException extends FormulaStatementException {

  public function __construct(string $message) {
    parent::__construct($message);
  }
}
