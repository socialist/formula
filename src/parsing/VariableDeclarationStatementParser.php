<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\VariableDeclarationStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class VariableDeclarationStatementParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedType = (new TypeParser())->parse($firstToken);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    $identifier = $token->value;
    if(!$token->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $token->next();
    if($token->id !== Token::ASSIGNMENT) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    $parsedInitilizer = (new ExpressionParser())->parse($token->next());
    if($parsedInitilizer->nextToken === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $parsedInitilizer->nextToken;
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $token);
    }
    return new ParserReturn(new VariableDeclarationStatement($parsedType->parsed, $identifier, $parsedInitilizer->parsed), $token->next());
  }
}
