<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\expression\BooleanExpression;
use TimoLehnertz\formula\expression\NullExpression;
use TimoLehnertz\formula\expression\Number;
use TimoLehnertz\formula\expression\StringLiteral;
use TimoLehnertz\formula\expression\TernaryExpression;
use TimoLehnertz\formula\expression\VariableExpression;
use TimoLehnertz\formula\operator\Operator;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ExpressionParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?FormulaPart {
    $insideBrackets = false;
    if($tokens[$index]->name === '(') {
      $insideBrackets = true;
    }
    $parts = [];
    for($index;$index < sizeof($tokens);$index++) {
      $token = $tokens[$index];
      switch($token->name) {
        case '(': // must be start of new formula
          $formulaExpression = ExpressionParser::parse($tokens, $index);
          if($formulaExpression === null) {
            return null;
          }
          $parts[] = $formulaExpression;
          break;
        case ')': // end of this or parent formulaExpression
          if($insideBrackets) { // ')' is part of this expression and should not be visible to the next parser
            $index++;
          }
          return ExpressionParser::buildExpression($parts, $insideBrackets);
        case ',': // end of this formula if nested
        case ':': // Ternary delimiter
        case '}': // Vector delimiter
          if(sizeof($parts) === 0 || $insideBrackets) {
            return null;
          }
          return ExpressionParser::buildExpression($parts, $insideBrackets);
        case '?': // Ternary delimiter
          $condition = ExpressionParser::buildExpression($parts, false);
          self::addIndex($tokens, $index);
          $leftExpression = self::parse($tokens, $index);
          if(!$leftExpression) throw new ExpressionNotFoundException("Invalid left ternary expression", $tokens, $index);
          if(sizeof($tokens) <= $index) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
          if($tokens[$index]->name != ":") throw new ExpressionNotFoundException("Expected \":\" (Ternary)", $tokens, $index);
          self::addIndex($tokens, $index);
          $rightExpression = self::parse($tokens, $index);
          if(!$rightExpression) throw new ExpressionNotFoundException("Invalid ternary expression", $tokens, $index);
          $parts = [
            new TernaryExpression($condition, $leftExpression, $rightExpression)
          ];
          $index--; // prevent $index++
          break;
        case 'B': // Boolean
          $parts[] = new BooleanExpression(strtolower($token->value) == "true");
          break;
        case 'O': // Operator
          $parts[] = Operator::fromString($token->value);
          break;
        case 'S': // String literal
          $parts[] = StringLiteral::fromToken($token);
          break;
        case 'null': // null
          $parts[] = new NullExpression();
          break;
        case 'N': // number
          $parts[] = new Number($token->value);
          break;
        case '{': // array
          $vector = ArrayParser::parseArray($tokens, $index); // will throw on error
          $parts[] = $vector;
          $index--; // prevent $index++
          break;
        case '[': // Array operator
          $arrayOperator = ArrayParser::parseOperator($tokens, $index); // will throw on error
          $parts[] = $arrayOperator;
          $index--; // prevent $index++
          break;
        case 'I': // either variable, method or assignment
          $method = MethodParser::parseMethod($tokens, $index);
          if($method !== null) {
            $parts[] = $method;
            $index--;
          } else {
            $parts[] = [
              new VariableExpression($token->value)
            ];
          }
          break;
      }
    }
    return new FormulaExpression($parts, $insideBrackets);
  }

  private static function buildExpression(array $parts, bool $insideBrackets): FormulaPart {}
}

