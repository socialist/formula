<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\ArrayExpression;
use TimoLehnertz\formula\operator\ArrayOperator;

/**
 * Array operator Syntax
 * '['<Expression>']'
 *
 * @author Timo Lehnertz
 */
class ArrayOperatorParser extends Parser {
  
  protected static function parsePart(array &$tokens, int &$index): ?ArrayExpression {
    if($tokens[$index]->value != "[") return null;
    if(sizeof($tokens) < $index + 3) return null;
    $index++;
    $indexExpression = ExpressionParser::parse($tokens, $index);
    if($indexExpression === null) return null;
    if(sizeof($tokens) <= $index) return null;
    if($tokens[$index]->value != "]") return null;
    $index++;
    return new ArrayOperator($indexExpression);
  }
}

