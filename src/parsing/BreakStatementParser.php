<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\BreakStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class BreakStatementParser extends Parser {

  public function __construct() {
    parent::__construct('break statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_BREAK) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_TOKEN, $token, 'Expected ;');
    }
    return new ParserReturn(new BreakStatement(), $token->next());
  }
}
