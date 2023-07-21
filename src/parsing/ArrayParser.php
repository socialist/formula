<?php
namespace src\parsing;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\expression\ArrayExpression;
use TimoLehnertz\formula\operator\ArrayOperator;
use TimoLehnertz\formula\parsing\FormulaExpressionParser;
use src\UnexpectedEndOfInputException;


class ArrayParser {
  
  public static function parseArray(array &$tokens, int &$index): ArrayExpression {
    if($tokens[$index]->value != '{') throw new ExpressionNotFoundException("Invalid Array");
    if(sizeof($tokens) < $index + 2) throw new ExpressionNotFoundException("Incomplete Array");
    $index++; // skipping {
    $elements = [];
    $first = true;
    for (; $index < sizeof($tokens); $index++) {
      $token = $tokens[$index];
      if($token->value == '}') {
        $index++;
        return new ArrayExpression($elements);
      }
      if($token->value == ',') {
        $index++;
        if($first || sizeof($tokens) < $index + 1) throw new ExpressionNotFoundException("Invalid Array");;
      }
      $expression = FormulaExpressionParser::parse($tokens, $index);
      if($expression === null) throw new ExpressionNotFoundException("Invalid element");
      $elements[] = $expression;
      $index--; // go back one to avoid increment
      $first = false;
    }
    throw new ExpressionNotFoundException("Invalid Vector: Unexpected end of input. Please add }");
  }
  
  public static function parseOperator(array &$tokens, int &$index): ArrayOperator {
    if($tokens[$index]->value != "[") throw new ExpressionNotFoundException("Invalid array operator", $tokens, $index);
    if(sizeof($tokens) < $index + 3) throw new ExpressionNotFoundException("Invalid array operator", $tokens, $index);
    $index++;
    $indexExpression = FormulaExpressionParser::parse($tokens, $index);
    if($indexExpression === null) throw new ExpressionNotFoundException("Invalid array operator expression", $tokens, $index);
    if(sizeof($tokens) <= $index) throw new UnexpectedEndOfInputException();
    if($tokens[$index]->value != "]") throw new ExpressionNotFoundException("Expected ']'", $tokens, $index);
    $index++;
    return new ArrayOperator($indexExpression);
  }
}

