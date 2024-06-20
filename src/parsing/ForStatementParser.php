<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\ForStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ForStatementParser extends Parser {

  public function __construct() {
    parent::__construct('for statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_FOR) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->requireNext();
    if($token->id !== Token::BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected (');
    }
    $token = $token->requireNext();
    /**
     * Declaration
     */
    $parsedDeclaration = null;
    try {
      $parsedDeclaration = (new VariableDeclarationStatementParser())->parse($token);
      $token = $parsedDeclaration->nextToken;
    } catch(ParsingSkippedException $e) {
      if($token->id !== Token::SEMICOLON) {
        throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected ;');
      }
      $token = $token->next();
    }
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    /**
     * Condition
     */
    $parsedCondition = null;
    try {
      $parsedCondition = (new ExpressionParser())->parse($token);
      $token = $parsedCondition->nextToken;
    } catch(ParsingSkippedException $e) {}
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected ;');
    }
    $token = $token->requireNext();
    /**
     * Increment
     */
    $parsedIncrement = null;
    try {
      if($token->id === Token::BRACKETS_CLOSED) {
        throw new ParsingSkippedException();
      }
      $parsedIncrement = (new ExpressionParser())->parse($token);
      $token = $parsedIncrement->nextToken;
      if($token === null) {
        throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
      }
    } catch(ParsingSkippedException $e) {}
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $firstToken, 'Expected )');
    }
    $token = $token->requireNext();
    /**
     * Body
     */
    $parsedBody = (new CodeBlockParser(true, false))->parse($token, true);
    $forStatement = new ForStatement($parsedDeclaration?->parsed ?? null, $parsedCondition?->parsed ?? null, $parsedIncrement?->parsed ?? null, $parsedBody->parsed);
    return new ParserReturn($forStatement, $parsedBody->nextToken);
  }
}
