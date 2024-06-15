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
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id === Token::SEMICOLON) {
      return new ParserReturn(new ContinueStatement(null), $token->next());
    } else {
      $parsedExpression = (new ExpressionParser())->parse($token, true);
      if($parsedExpression->nextToken === null) {
        throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
      }
      $token = $parsedExpression->nextToken;
      if($token->id !== Token::SEMICOLON) {
        throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_TOKEN, $token, 'Expected ;');
      }
      return new ParserReturn(new ContinueStatement($parsedExpression->parsed), $token->next());
    }
  }
}
