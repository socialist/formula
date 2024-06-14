<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ReturnStatementParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_RETURN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
    }
    if($token->id === Token::SEMICOLON) {
      return new ParserReturn(new ReturnStatement(null), $token->next());
    } else {
      $parsedExpression = (new ExpressionParser())->parse($token);
      if($parsedExpression->nextToken === null) {
        throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
      }
      $token = $parsedExpression->nextToken;
      if($token->id !== Token::SEMICOLON) {
        throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $token);
      }
      return new ParserReturn(new ReturnStatement($parsedExpression->parsed), $token->next());
    }
  }
}
