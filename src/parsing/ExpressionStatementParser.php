<?php
namespace src\parsing;

use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\statement\ExpressionStatement;
use TimoLehnertz\formula\tokens\Token;

class ExpressionStatementParser extends Parser {

  /**
   *
   * @param Token[] $tokens
   */
  protected static function parsePart(array &$tokens, int &$index): ?ExpressionStatement {
    $expression = ExpressionParser::parse($tokens, $index);
    if($expression === null) return null;
    if($index >= sizeof($tokens)) {
      return null;
    }
    $token = $tokens[$index];
    if($token->name === ';') {
      return new ExpressionStatement($expression);
    } else {
      return null;
    }
  }
}

