<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaBugException extends \Exception {

  public function __construct(string $message) {
    parent::__construct($message);
  }
}
