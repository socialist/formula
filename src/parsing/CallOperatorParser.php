<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\CallExpression;
use TimoLehnertz\formula\operator\CallOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class CallOperatorParser extends EnumeratedParser {

  public function __construct() {
    parent::__construct(new ExpressionParser(), Token::BRACKETS_OPEN, Token::COMMA, Token::BRACKETS_CLOSED, false, true);
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $result = parent::parsePart($firstToken);
    return new ParserReturn(new CallOperator(new CallExpression($result->parsed)), $result->nextToken);
  }
}
