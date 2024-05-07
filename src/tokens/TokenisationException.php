<?php
namespace src\tokens;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class TokenisationException extends \Exception {

  public function __construct(string $message, int $line, int $position) {
    parent::__construct("unexpected symbol at: $line:$position. $message");
  }
}

