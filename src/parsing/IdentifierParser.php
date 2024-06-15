<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class IdentifierParser extends Parser {

  public function __construct() {
    parent::__construct('identifier expression');
  }

  protected function parsePart(Token $firstToken, bool $topLevel = true): ParserReturn {
    if($firstToken->id === Token::IDENTIFIER) {
      return new ParserReturn(new IdentifierExpression($firstToken->value), $firstToken->next());
    } else {
      throw new ParsingSkippedException();
    }
  }
}
