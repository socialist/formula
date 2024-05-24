<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaValidationException extends \Exception {

  public function __construc(string $message) {
    parent::__construct($message);
  }
}