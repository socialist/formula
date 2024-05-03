<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\ParsingException;

/**
 * Statement ::= <> | <> | <>
 *
 * @author Timo Lehnertz
 *        
 */
class StatementParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): FormulaPart {
    $expression = ExpressionParser::parse($tokens, $index);
    if($expression) {
      return $expression;
    }
    throw new ParsingException('Expected statement. Got "'.$tokens[$index]->value.'"', $tokens[$index]);
  }
}

