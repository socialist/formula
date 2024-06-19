<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\statement\WhileStatement;

/**
 * @author Timo Lehnertz
 */
class WhileStatementParser extends Parser {

  public function __construct() {
    parent::__construct('while statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_WHILE) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected (');
    }
    $token = $token->next();
    $parsedCondition = (new ExpressionParser())->parse($token, true);
    $token = $parsedCondition->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected )');
    }
    $token = $token->next();
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $parsedBody = (new CodeBlockParser(true, false))->parse($token, true);
    $whileStatement = new WhileStatement($parsedCondition->parsed, $parsedBody->parsed);

    return new ParserReturn($whileStatement, $parsedBody->nextToken);
  }
}
