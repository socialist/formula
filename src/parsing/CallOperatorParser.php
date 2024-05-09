<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\CallOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class CallOperatorParser extends EnumeratedParser {

  public function __construct() {
    parent::__construct(new ExpressionParser(), Token::BRACKETS_OPEN, Token::COMMA, Token::BRACKETS_CLOSED, false, true);
  }

  protected function parsePart(Token $firstToken): ParserReturn|int {
    $args = parent::parsePart($firstToken);
    if(is_int($args)) {
      return $args;
    }
    return new CallOperator($args);
  }
}
