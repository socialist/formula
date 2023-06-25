<?php
namespace TimoLehnertz\formula\procedure;

class DoublicateVariableException extends \Exception {
  
  public function __construct(string $message) {
    parent::__construct($message);
  }
}

