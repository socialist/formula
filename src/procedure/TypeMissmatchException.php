<?php
namespace src\procedure;

class TypeMissmatchException extends \Exception {
  
  public function __construct(string $message) {
    parent::__construct($message);
  }
}

