<?php
namespace src\parsing;

use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\statement\ExpressionStatement;

class ExpressionStatementParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?ExpressionStatement {
    $expression = ExpressionParser::parse($tokens, $index);
    if($expression === null) return null;
    return new ExpressionStatement($expression);
  }
}

