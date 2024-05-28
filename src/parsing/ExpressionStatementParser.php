<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\ExpressionStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ExpressionStatementParser extends Parser {

  /**
   * @param Token[] $tokens
   */
  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedExpression = (new ExpressionParser())->parse($firstToken);
    if(is_int($parsedExpression)) {
      return $parsedExpression;
    }
    if($parsedExpression->nextToken === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    if($parsedExpression->nextToken->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_SEMICOLON, $parsedExpression->nextToken);
    }
    return new ParserReturn(new ExpressionStatement($parsedExpression->parsed), $parsedExpression->nextToken->next());
  }
}
