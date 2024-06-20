<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperatorParser extends Parser {

  public function __construct() {
    parent::__construct('typecast operator');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id != Token::BRACKETS_OPEN) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->requireNext();
    $parsedType = (new TypeParser(false))->parse($token);
    $token = $parsedType->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingSkippedException();
    }
    return new ParserReturn(new TypeCastOperator(true, $parsedType->parsed), $token->next());
  }
}
