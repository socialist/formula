<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\IfStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class IfStatementParser extends Parser {

  private readonly bool $isFirst;

  public function __construct(bool $isFirst = true) {
    $this->isFirst = $isFirst;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedCondition = null;
    try {
      if($firstToken->id !== Token::KEYWORD_IF) {
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
    } catch(ParsingException $e) {
      if($this->isFirst) {
        throw $e;
      } else { // try parse else block
        $token = $firstToken;
        $parsedCondition = null;
      }
    }
    $parsedBody = (new CodeBlockParser(true, false))->parse($token);
    if(!$this->isFirst) {}
    $token = $parsedBody->nextToken;
    $parsedElse = null;
    if($token !== null && $token->id === Token::KEYWORD_ELSE) {
      if($parsedCondition !== null) {
        throw new ParsingException(ParsingException::PARSING_ERROR_TOO_MANY_ELSE, $token);
      }
      $token = $token->next();
      $parsedElse = (new IfStatementParser(false))->parse($token);
      $token = $parsedElse->nextToken;
    }
    $ifStatement = new IfStatement($parsedCondition?->parsed ?? null, $parsedBody->parsed, $parsedElse?->parsed ?? null);
    return new ParserReturn($ifStatement, $token);
  }
}
