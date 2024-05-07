<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ArrayExpression;
use TimoLehnertz\formula\operator\ArrayOperator;
use TimoLehnertz\formula\tokens\Token;

/**
 * Array operator Syntax
 * '['<Expression>']'
 *
 * @author Timo Lehnertz
 */
class ArrayOperatorParser extends Parser {
  
  protected static function parsePart(Token &$token): ?ArrayExpression {
    if($token->id != Token::SQUARE_BRACKETS_OPEN) {
      return null;
    }
    $token = $token->requireNext();
    $indexExpression = ExpressionParser::parse($token);
    if($indexExpression === null) {
      return null;
    }
    if($token->id != Token::SQUARE_BRACKETS_CLOSED) {
      return null;
    }
    $token = $token->next();
    return new ArrayOperator($indexExpression);
  }
}

