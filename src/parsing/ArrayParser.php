<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ArrayExpression;
use TimoLehnertz\formula\expression\Expression;

/**
 * Array Syntax
 * Array ::= '{' <Elements> '}' | '{' '}'
 * Elements ::= <Element> | <Element> ',' <Elements>
 * 
 * @author Timo Lehnertz
 */
class ArrayParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?Expression {
    if($tokens[$index]->value != '{') return null;
    if(sizeof($tokens) < $index + 2) return null;
    $index++; // skipping {
    $elements = [];
    $first = true;
    for (; $index < sizeof($tokens); $index++) {
      $token = $tokens[$index];
      if($token->value === '}') {
        $index++;
        return new ArrayExpression($elements);
      }
      if($token->value === ',') {
        $index++;
        if($first || sizeof($tokens) < $index + 1) return null;
      }
      $expression = ExpressionParser::parse($tokens, $index);
      if($expression === null) return null;
      $elements[] = $expression;
      $index--; // go back one to avoid increment
      $first = false;
    }
    return null;
  }
}

