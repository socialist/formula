<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class IdentifierParser extends Parser {

  protected function parsePart(Token $firstToken, bool $topLevel = true): ParserReturn {
    if($firstToken->id === Token::IDENTIFIER) {
      return new ParserReturn(new IdentifierExpression($firstToken->value), $firstToken->next());
    } else {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
  }
}
