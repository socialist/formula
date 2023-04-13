<?php
/**
 * @author Timo Lehnertz
 */
namespace TimoLehnertz\formula;

class NullpointerException extends \Exception {
  
  public function __construct(string $message) {
    parent::__construct($message);
  }
}

