<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringValue;

/**
 * @author Timo Lehnertz
 */
class ConstantExpressionParser extends Parser {

  public function __construct() {
    parent::__construct('constant expression');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    switch($firstToken->id) {
      case Token::FLOAT_CONSTANT:
        return new ParserReturn(new ConstantExpression(new FloatValue(floatval($firstToken->value))), $firstToken->next());
      case Token::INT_CONSTANT:
        return new ParserReturn(new ConstantExpression(new IntegerValue(intval($firstToken->value))), $firstToken->next());
      case Token::KEYWORD_FALSE:
        return new ParserReturn(new ConstantExpression(new BooleanValue(false)), $firstToken->next());
      case Token::KEYWORD_TRUE:
        return new ParserReturn(new ConstantExpression(new BooleanValue(true)), $firstToken->next());
      case Token::STRING_CONSTANT:
        return new ParserReturn(new ConstantExpression(new StringValue($firstToken->value)), $firstToken->next());
      case Token::KEYWORD_NULL:
        return new ParserReturn(new ConstantExpression(new NullValue()), $firstToken->next());
    }
    throw new ParsingSkippedException();
  }
}
