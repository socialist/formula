<?php
namespace TimoLehnertz\formula\procedure;

class DoublicateMethodException extends \Exception {
  
  public function __construct(string $message) {
    parent::__construct($message);
  }
}

