<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class IdentifierParser extends Parser {

  protected function parsePart(Token $firstToken, bool $topLevel = true): ParserReturn|int {
    if($firstToken->id === Token::IDENTIFIER) {
      return new ParserReturn(new IdentifierExpression($firstToken->value), $firstToken->next());
    } else {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
  }
}
