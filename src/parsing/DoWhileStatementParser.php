<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\DoWhileStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class DoWhileStatementParser extends Parser {

  public function __construct() {
    parent::__construct('do while statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_DO) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->next();
    $parsedBody = (new CodeBlockParser(false, false))->parse($token);
    $token = $parsedBody->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::KEYWORD_WHILE) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected while');
    }
    $token = $token->requireNext();
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
    $token = $token->requireNext();
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected ;');
    }
    $doWileStatement = new DoWhileStatement($parsedBody->parsed, $parsedCondition->parsed);
    return new ParserReturn($doWileStatement, $token->next());
  }
}
