<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * Array Syntax
 * Array ::= '{' <Elements> '}' | '{' '}'
 * Elements ::= <Element> | <Element> ',' <Elements>
 *
 * @author Timo Lehnertz
 */
class SimpleOperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn|int {}
}
