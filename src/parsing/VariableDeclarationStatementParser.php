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
    if(is_int($parsedType)) {
      return $parsedType;
    }
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    if(!$token->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $token->next();
    if($token->id !== Token::ASSIGNMENT) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    $parsedInitilizer = (new ExpressionParser())->parse($token->next());
    if($parsedInitilizer->nextToken === null || !$parsedInitilizer->nextToken->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $parsedInitilizer->nextToken->next();
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $token);
    }
    return new ParserReturn(new VariableDeclarationStatement($parsedInitilizer->parsed), $token->next());
  }
}
