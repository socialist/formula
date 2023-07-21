<?php
namespace src;

class UnexpectedEndOfInputException extends \Exception {
  
  public function __construct() {
    parent::__construct('Unexpected end of input');
  }
}