<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\expression\ArrayExpression;

/**
 * Array Syntax
 * Array ::= '{' <Elements> '}' | '{' '}'
 * Elements ::= <Element> | <Element> ',' <Elements>
 *
 * @author Timo Lehnertz
 */
class ArrayParser extends EnumeratedParser {

  public function __construct() {
    parent::__construct('array expression', new ExpressionParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, true);
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsed = parent::parsePart($firstToken);
    return new ParserReturn(new ArrayExpression($parsed->parsed), $parsed->nextToken);
  }
}
