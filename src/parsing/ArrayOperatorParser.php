<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\ArrayAccessOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * Array operator Syntax
 * '['<Expression>']'
 *
 * @author Timo Lehnertz
 */
class ArrayOperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id != Token::SQUARE_BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    if(!$firstToken->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $firstToken->next();
    $parsedIndexExpression = (new ExpressionParser())->parse($token);
    $token = $parsedIndexExpression->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    if($token->id !== Token::SQUARE_BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $token);
    }
    return new ParserReturn(new ArrayAccessOperator($parsedIndexExpression->parsed, $token->next()));
  }
}
