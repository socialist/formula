<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\BreakStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class BreakStatementParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_BREAK) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
    }
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $token);
    }
    return new ParserReturn(new BreakStatement(), $token->next());
  }
}
