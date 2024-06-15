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
    $token = $firstToken->next();
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::BRACKETS_OPEN) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED, $firstToken, 'Expected (');
    }
    $token = $token->next();
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    /**
     * Declaration
     */
    $parsedDeclaration = null;
    try {
      $parsedDeclaration = (new VariableDeclarationStatementParser())->parse($token);
      $token = $parsedDeclaration->nextToken;
    } catch(ParsingSkippedException $e) {
      if($token->id !== Token::SEMICOLON) {
        throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED, $firstToken, 'Expected ;');
      }
      $token = $token->next();
    }
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
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
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED, $firstToken, 'Expected ;');
    }
    $token = $token->next();
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
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
        throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
      }
    } catch(ParsingSkippedException $e) {}
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED, $firstToken, 'Expected )');
    }
    $token = $token->next();
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    /**
     * Body
     */
    $parsedBody = (new CodeBlockParser(true, false))->parse($token, true);
    $forStatement = new ForStatement($parsedDeclaration?->parsed ?? null, $parsedCondition?->parsed ?? null, $parsedIncrement?->parsed ?? null, $parsedBody->parsed);
    return new ParserReturn($forStatement, $parsedBody->nextToken);
  }
}
