<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\BracketExpression;

/**
 * @author Timo Lehnertz
 */
class ExpressionParser extends Parser {

  // @formatter:off
  private static array $expressionEndingTokens = [
    Token::COMMA => true,
    Token::BRACKETS_CLOSED => true,
    Token::SQUARE_BRACKETS_CLOSED => true,
    Token::CURLY_BRACKETS_CLOSED => true,
    Token::COlON => true,
    Token::SEMICOLON => true,
  ];

  // @formatter:on
  protected function parsePart(Token $firstToken, bool $topLevel = true): ParserReturn|int {
    $token = $firstToken;
    $inBrackets = false;
    if(!$topLevel) {
      $inBrackets = $token->id === Token::BRACKETS_OPEN;
    }
    if($inBrackets) {
      $token = $token->next();
    }
    $expressionsAndOperators = [];
    $variantParser = new VariantParser([new OperatorParser(),new ConstantExpressionParser(),new ArrayParser(),new IdentifierParser()]);
    while($token !== null) {
      if(isset(ExpressionParser::$expressionEndingTokens[$token->id])) {
        break;
      }
      $result = $variantParser->parse($token);
      if(!is_int($result)) {
        $expressionsAndOperators[] = $result->parsed;
        $token = $result->nextToken;
      } else if($token->id === Token::BRACKETS_OPEN) {
        $result = $this->parsePart($token, false);
        if(is_int($result)) {
          return $result;
        }
        $token = $result->nextToken;
        $expressionsAndOperators[] = $result->parsed;
      } else {
        return ParsingException::PARSING_ERROR_GENERIC;
      }
    }
    if($inBrackets) {
      if($token === null || $token->id !== Token::BRACKETS_CLOSED) {
        throw ParsingException::PARSING_ERROR_GENERIC;
      }
      $token = $token->next();
    }
    $result = $this->transform($expressionsAndOperators, $token);
    if($inBrackets && !is_int($result)) {
      $result = new ParserReturn(new BracketExpression($result->parsed), $result->nextToken);
    }
    return $result;
  }

  private function transform(array $expressionsAndOperators, ?Token $nextToken): ParserReturn|int {
    while(true) {
      // find lowest precedence operator
      $operator = null;
      $index = -1;
      foreach($expressionsAndOperators as $i => $expressionsOrOperator) {
        if($expressionsOrOperator instanceof Operator) {
          if($operator === null || $expressionsOrOperator->getPrecedence() < $operator->getPrecedence()) {
            $operator = $expressionsOrOperator;
            $index = $i;
          }
        }
      }
      if($operator === null) {
        break; // no operators left
      }
      // find left and right operand
      $leftExpression = null;
      $rightExpression = null;
      if($index > 0 && $expressionsAndOperators[$index - 1] instanceof Expression) {
        $leftExpression = $expressionsAndOperators[$index - 1];
      }
      if($index + 1 < count($expressionsAndOperators) && $expressionsAndOperators[$index + 1] instanceof Expression) {
        $rightExpression = $expressionsAndOperators[$index + 1];
      }
      // check if set correctly
      switch($operator->getOperatorType()) {
        case OperatorType::PrefixOperator:
          if($rightExpression === null) {
            return ParsingException::PARSING_ERROR_INVALID_OPERATOR_USE;
          }
          $startingIndex = $index;
          $size = 2;
          break;
        case OperatorType::InfixOperator:
          if($leftExpression === null || $rightExpression === null) {
            return ParsingException::PARSING_ERROR_INVALID_OPERATOR_USE;
          }
          $startingIndex = $index - 1;
          $size = 3;
          break;
        case OperatorType::PostfixOperator:
          if($leftExpression === null) {
            return ParsingException::PARSING_ERROR_INVALID_OPERATOR_USE;
          }
          $startingIndex = $index - 1;
          $size = 2;
          break;
      }
      // combine operator and operands into OperatorExpression
      $operatorExpression = new OperatorExpression($leftExpression, $operator, $rightExpression);
      // insert OperatorExpression replacing original content
      array_splice($expressionsAndOperators, $startingIndex, $size, [$operatorExpression]);
    }
    if(count($expressionsAndOperators) !== 1) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    return new ParserReturn($expressionsAndOperators[0], $nextToken);
  }
}
