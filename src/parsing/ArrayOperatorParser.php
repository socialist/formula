<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\operator\ArrayOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * Array operator Syntax
 * '['<Expression>']'
 *
 * @author Timo Lehnertz
 */
class ArrayOperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn|int {
    if($firstToken->id != Token::SQUARE_BRACKETS_OPEN) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    $token = $firstToken->requireNext();
    $parsedIndexExpression = (new ExpressionParser())->parse($token);
    if(is_int($parsedIndexExpression)) {
      return $parsedIndexExpression;
    }
    $token = $parsedIndexExpression->nextToken;
    if($token === null) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    if($token->id !== Token::SQUARE_BRACKETS_CLOSED) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    return new ParserReturn(new ArrayOperator($parsedIndexExpression->parsed, $token->next()));
  }
}
