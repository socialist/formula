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

  public function __construct() {
    parent::__construct('array operator');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id != Token::SQUARE_BRACKETS_OPEN) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->requireNext();
    $parsedIndexExpression = (new ExpressionParser())->parse($token, true);
    $token = $parsedIndexExpression->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::SQUARE_BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $token, 'Expected ]');
    }
    return new ParserReturn(new ArrayAccessOperator($parsedIndexExpression->parsed), $token->next());
  }
}
