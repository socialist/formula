<?php
namespace socialistFork\formula;

use socialistFork\formula\tokens\Token;

/**
 *
 * @author Timo Lehnertz
 * 
 */
class ParsingException extends \Exception {
  public function __construct(string $message, Token $token) {
    parent::__construct("unexpected symbol \"$token->value\" at: $token->position. Message: $message");
  }
}