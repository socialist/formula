<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ArgumentListExpression;
use TimoLehnertz\formula\operator\CallOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class CallOperatorParser extends EnumeratedParser {

  public function __construct() {
    parent::__construct('call operator', new ExpressionParser(), Token::BRACKETS_OPEN, Token::COMMA, Token::BRACKETS_CLOSED, false, true);
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $prev = $firstToken->prev();
    if($prev === null || $prev->id !== Token::IDENTIFIER) {
      throw new ParsingSkippedException();
    }
    $result = parent::parsePart($firstToken);
    return new ParserReturn(new CallOperator(new ArgumentListExpression($result->parsed)), $result->nextToken);
  }
}
