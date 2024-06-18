<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\expression\MemberAccsessExpression;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class IdentifierParser extends Parser {

  public function __construct() {
    parent::__construct('identifier expression');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::IDENTIFIER) {
      throw new ParsingSkippedException();
    }
    if($firstToken->hasPrev() && $firstToken->prev()->id === Token::DOT) {
      $parsed = new MemberAccsessExpression($firstToken->value);
    } else {
      $parsed = new IdentifierExpression($firstToken->value);
    }
    return new ParserReturn($parsed, $firstToken->next());
  }
}
