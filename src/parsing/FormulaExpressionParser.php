<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\expression\BooleanExpression;
use TimoLehnertz\formula\expression\FormulaExpression;
use TimoLehnertz\formula\expression\NullExpression;
use TimoLehnertz\formula\expression\Number;
use TimoLehnertz\formula\expression\StringLiteral;
use TimoLehnertz\formula\expression\TernaryExpression;
use TimoLehnertz\formula\expression\VariableExpression;
use TimoLehnertz\formula\operator\Operator;
use src\UnexpectedEndOfInputException;
use src\parsing\ArrayParser;
use src\parsing\MethodParser;


class FormulaExpressionParser implements Parseable {
  
  public static function parse(array &$tokens, int &$index): ?FormulaExpression {
    if(sizeof($tokens) <= $index) {
      return null;
    }
    $startIndex = $index;
    $insideBrackets = false;
    if($tokens[$index]->name === '(') {
      $insideBrackets = true;
    }
    $formulaExpression = new FormulaExpression();
//     $this->tokens = $tokens;
    $expressionsAndOperators = [];
    for($index;$index < sizeof($tokens);$index++) {
      $token = $tokens[$index];
      //       echo "top level:".$this->topLevel.", token: ".$token["name"].", index: $index".PHP_EOL;
      switch($token->name) {
        case '(': // must be start of new formula
          $formulaExpression = self::parse($tokens, $index);
          if($formulaExpression === null) {
            $index = $startIndex;
            return null;
          }
          $expressionsAndOperators[] = $formulaExpression;
          break;
        case ')': // end of this or parent formulaExpression
          if($insideBrackets) { // ')' is part of this expression and should not be visible to the next parser
            $index++;
          }
          return new FormulaExpression($expressionsAndOperators, $insideBrackets);
        case ',': // end of this formula if nested
        case ':': // Ternary delimiter
        case '}': // Vector delimiter
        case ',': // Vector element delimiter
        case ']': // Array operator end
          if(sizeof($expressionsAndOperators) === 0 || $insideBrackets) {
            $index = $startIndex;
            return null;
          }
          return new FormulaExpression($expressionsAndOperators, $insideBrackets);
        case '?': // Ternary delimiter
          $condition = new FormulaExpression($expressionsAndOperators, false);
          self::addIndex($tokens, $index);
          $leftExpression = self::parse($tokens, $index);
          if(!$leftExpression) throw new ExpressionNotFoundException("Invalid left ternary expression", $tokens, $index);
          if(sizeof($tokens) <= $index) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
          if($tokens[$index]->name != ":") throw new ExpressionNotFoundException("Expected \":\" (Ternary)", $tokens, $index);
          self::addIndex($tokens, $index);
          $rightExpression = self::parse($tokens, $index);
          if(!$rightExpression) throw new ExpressionNotFoundException("Invalid ternary expression", $tokens, $index);
          $expressionsAndOperators = [new TernaryExpression($condition, $leftExpression, $rightExpression)];
          $index--; // prevent $index++
          break;
        case 'B': // Boolean
          $expressionsAndOperators[] = new BooleanExpression(strtolower($token->value) == "true");
          break;
        case 'O': // Operator
          $expressionsAndOperators[] = Operator::fromString($token->value);
          break;
        case 'S': // String literal
          $expressionsAndOperators[] = StringLiteral::fromToken($token);
          break;
        case 'null': // null
          $expressionsAndOperators[] = new NullExpression();
          break;
        case 'N': // number
          $expressionsAndOperators[] = new Number($token->value);
          break;
        case '{': // array
          $vector = ArrayParser::parseArray($tokens, $index); // will throw on error
          $expressionsAndOperators[] = $vector;
          $index--; // prevent $index++
          break;
        case '[': // Array operator
          $arrayOperator = ArrayParser::parseOperator($tokens, $index); // will throw on error
          $expressionsAndOperators[] = $arrayOperator;
          $index--; // prevent $index++
          break;
        case 'I': // either variable, method or assignment
          $method = MethodParser::parseMethod($tokens, $index);
          if($method !== null) {
            $expressionsAndOperators[] = $method;
            $index--;
          } else {
            $expressionsAndOperators[] = [new VariableExpression($token->value)];
          }
          break;
      }
    }
    return new FormulaExpression($expressionsAndOperators, $insideBrackets);
  }
  
  private static function addIndex(array &$tokens, int &$index): void {
    $index++;
    if(sizeof($tokens) <= $index) throw new UnexpectedEndOfInputException();
  }
}

