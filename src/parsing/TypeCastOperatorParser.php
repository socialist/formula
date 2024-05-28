<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id != Token::BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    if(!$firstToken->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $firstToken->next();
    $parsedType = (new TypeParser())->parse($token);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    return new ParserReturn(new TypeCastOperator(true, $parsedType->parsed), $token->next());
  }
}
