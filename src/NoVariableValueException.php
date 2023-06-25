<?php
namespace TimoLehnertz\formula;

class NoVariableValueException extends \Exception {
  
  public function __construct(string $message) {
    parent::__construct($message);
  }
}
