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
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\FloatType;

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
        return new ParserReturn(new ConstantExpression(new FloatType(true), new FloatValue(floatval($firstToken->value))), $firstToken->next());
      case Token::INT_CONSTANT:
        return new ParserReturn(new ConstantExpression(new IntegerType(true), new IntegerValue(intval($firstToken->value))), $firstToken->next());
      case Token::KEYWORD_FALSE:
        return new ParserReturn(new ConstantExpression(new BooleanType(true), new BooleanValue(false)), $firstToken->next());
      case Token::KEYWORD_TRUE:
        return new ParserReturn(new ConstantExpression(new BooleanType(true), new BooleanValue(true)), $firstToken->next());
      case Token::STRING_CONSTANT:
        return new ParserReturn(new ConstantExpression(new StringType(true), new StringValue($firstToken->value)), $firstToken->next());
      case Token::KEYWORD_NULL:
        return new ParserReturn(new ConstantExpression(new NullType(true), new NullValue()), $firstToken->next());
    }
    throw new ParsingSkippedException();
  }
}
