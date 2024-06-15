<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\VariableDeclarationStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class VariableDeclarationStatementParser extends Parser {

  public function __construct() {
    parent::__construct('variable declaration statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedType = (new TypeParser())->parse($firstToken);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingSkippedException();
    }
    $identifier = $token->value;
    if(!$token->hasNext()) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $token = $token->next();
    if($token->id !== Token::ASSIGNMENT) {
      throw new ParsingSkippedException();
    }
    $parsedInitializer = (new ExpressionParser())->parse($token->next(), true);
    if($parsedInitializer->nextToken === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $token = $parsedInitializer->nextToken;
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $token);
    }
    return new ParserReturn(new VariableDeclarationStatement($parsedType->parsed, $identifier, $parsedInitializer->parsed), $token->next());
  }
}
