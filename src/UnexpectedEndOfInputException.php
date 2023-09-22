<?php
namespace TimoLehnertz\formula;

class UnexpectedEndOfInputException extends \Exception {
  
  public function __construct() {
    parent::__construct('Unexpected end of input');
  }
}