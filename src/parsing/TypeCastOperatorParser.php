<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\operator\ArrayOperator;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\operator\TypeCastOperator;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn|int {
    if($firstToken->id != Token::BRACKETS_OPEN) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    $token = $firstToken->requireNext();
    $parsedType = (new TypeParser())->parse($token);
    if(is_int($parsedType)) {
      return $parsedType;
    }
    $token = $parsedType->nextToken;
    if($token === null) {
      return ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT;
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    return new ParserReturn(new TypeCastOperator(true, $parsedType->parsed), $token->next());
  }
}
