<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\statement\ContinueStatement;

/**
 * @author Timo Lehnertz
 */
class ContinueStatementParser extends Parser {

  public function __construct() {
    parent::__construct('continue statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_CONTINUE) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_TOKEN, $token, 'Expected ;');
    } else {
      return new ParserReturn(new ContinueStatement(), $token->next());
    }
  }
}
