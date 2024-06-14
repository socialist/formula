<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\statement\WhileStatement;

/**
 * @author Timo Lehnertz
 */
class WhileStatementParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_WHILE) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $firstToken);
    }
    if($token->id !== Token::BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED, $firstToken, Token::BRACKETS_OPEN);
    }
    $token = $token->next();
    $parsedCondition = (new ExpressionParser())->parse($token);
    $token = $parsedCondition->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED, $firstToken, Token::BRACKETS_CLOSED);
    }
    $token = $token->next();
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
    }
    $parsedBody = (new CodeBlockParser(true, false))->parse($token);
    $whileStatement = new WhileStatement($parsedCondition->parsed, $parsedBody->parsed);

    return new ParserReturn($whileStatement, $parsedBody->nextToken);
  }
}
