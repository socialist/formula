<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\ExpressionStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ExpressionStatementParser extends Parser {

  public function __construct() {
    parent::__construct('expression statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsedExpression = (new ExpressionParser())->parse($firstToken);
    if($parsedExpression->nextToken === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($parsedExpression->nextToken->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $parsedExpression->nextToken, 'Expected ;');
    }
    return new ParserReturn(new ExpressionStatement($parsedExpression->parsed), $parsedExpression->nextToken->next());
  }
}
