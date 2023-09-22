<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\expression\MethodExpression;

/**
 * @deprecated replaced by operator
 */
class MethodParser extends Parser {
  
  /**
   * @deprecated
   */
  protected static function parsePart(array &$tokens, int &$index): ?MethodExpression {
    // identifier
    if($tokens[$index]->name != "I") return null;
    if(sizeof($tokens) <= $index + 2) return null; // must be variable as there are no parameters following
    if($tokens[$index + 1]->name != "(") return null; // must be variable
    $identifier = $tokens[$index]->value;
    // parse parameters
    $index += 2; // skipping identifier and opening bracket
    $parameterExpressions = [];
    $first = true;
    for ($index; $index < sizeof($tokens); $index++) {
      $token = $tokens[$index];
      if($token->name == ')') {
        $index++;
        return new MethodExpression($identifier, $parameterExpressions);
      }
      if($first && $token->name == ',') throw new ParsingException("", $token);
      if(!$first && $token->name != ',') throw new ParsingException("", $token);
      if(!$first) $index++;
      $param = ExpressionParser::parse($tokens, $index);
      if($param === null) throw new ExpressionNotFoundException("Invalid Method argument", $tokens, $index);
      $parameterExpressions []= $param;
      $index--; // skipping increment
      $first = false;
    }
    throw new UnexpectedEndOfInputException();
  }
}

