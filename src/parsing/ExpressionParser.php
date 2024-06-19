<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\expression\BracketExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\TernaryExpression;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\operator\ParsedOperator;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\PrettyPrintOptions;
use function PHPUnit\Framework\assertFalse;

/**
 * @author Timo Lehnertz
 */
class ExpressionParser extends Parser {

  private readonly bool $forceBrackets;

  /**
   * indicates if this is the parent expression
   */
  private readonly bool $topLevel;

  public function __construct(bool $forceBrackets = false, bool $topLevel = true) {
    parent::__construct('expression');
    $this->forceBrackets = $forceBrackets;
    $this->topLevel = $topLevel;
    if($topLevel) {
      assertFalse($this->forceBrackets);
    }
  }

  // @formatter:off
  private static array $expressionEndingTokens = [
    Token::COMMA => true,
    Token::BRACKETS_CLOSED => true,
    Token::SQUARE_BRACKETS_CLOSED => true,
    Token::CURLY_BRACKETS_CLOSED => true,
    Token::COlON => true,
    Token::SEMICOLON => true,
    Token::QUESTIONMARK => true,
  ];

  // @formatter:on
  protected function parsePart(Token $firstToken): ParserReturn {
    if($this->forceBrackets && $firstToken->id !== Token::BRACKETS_OPEN) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken;
    $inBrackets = false;
    if(!$this->topLevel) {
      $inBrackets = $token->id === Token::BRACKETS_OPEN;
      if($inBrackets) {
        $token = $token->requireNext();
      }
    }
    $ternaryCondition = null;
    $ternaryLeftExpression = null;
    $expressionsAndOperators = [];
    $variantParser = new VariantParser($this->name, [new OperatorParser(),new ConstantExpressionParser(),new ArrayParser(),new IdentifierParser(),new ExpressionParser(true, false),new FunctionParser(false)]);
    while($token !== null) {
      // Ternary
      if($ternaryCondition === null && $token->id === Token::QUESTIONMARK) {
        $ternaryCondition = $this->transform($expressionsAndOperators, $token);
        $expressionsAndOperators = [];
        if(!$token->hasNext()) {
          throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
        }
        $token = $token->next();
      }
      // Still ternary
      if($ternaryCondition !== null && $ternaryLeftExpression === null && $token->id === Token::COlON) {
        $ternaryLeftExpression = $this->transform($expressionsAndOperators, $token);
        $expressionsAndOperators = [];
        if(!$token->hasNext()) {
          throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
        }
        $token = $token->next();
      }
      if(isset(ExpressionParser::$expressionEndingTokens[$token->id])) {
        break;
      }
      $result = $variantParser->parse($token);
      $expressionsAndOperators[] = $result->parsed;
      $token = $result->nextToken;
    }
    if($inBrackets) {
      if($token === null) {
        throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
      }
      if($token->id !== Token::BRACKETS_CLOSED) {
        throw new ParsingSkippedException();
      }
      $token = $token->next();
    }
    $result = $this->transform($expressionsAndOperators, $token);
    if($ternaryCondition !== null) {
      if($ternaryLeftExpression === null) {
        throw new ParsingException(ParsingException::ERROR_INCOMPLETE_TERNARY, $token);
      }
      $result = new TernaryExpression($ternaryCondition, $ternaryLeftExpression, $result);
    }
    if($inBrackets) {
      $result = new BracketExpression($result);
    }
    return new ParserReturn($result, $token);
  }

  private function transform(array $expressionsAndOperators, ?Token $nextToken): Expression {
    if(count($expressionsAndOperators) === 0) {
      throw new ParsingSkippedException();
    }
    while(true) {
      // find lowest precedence operator
      $operator = null;
      $index = -1;
      foreach($expressionsAndOperators as $i => $expressionsOrOperator) {
        if($expressionsOrOperator instanceof ParsedOperator) {
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
            throw new ParsingException(ParsingException::ERROR_INVALID_OPERATOR_USE, $nextToken);
          }
          $startingIndex = $index;
          $size = 2;
          break;
        case OperatorType::InfixOperator:
          if($leftExpression === null || $rightExpression === null) {
            throw new ParsingException(ParsingException::ERROR_INVALID_OPERATOR_USE, $nextToken);
          }
          $startingIndex = $index - 1;
          $size = 3;
          break;
        case OperatorType::PostfixOperator:
          if($leftExpression === null) {
            throw new ParsingException(ParsingException::ERROR_INVALID_OPERATOR_USE, $nextToken, 'Missing left expression of postfix operator '.$operator->toString(PrettyPrintOptions::buildDefault()));
          }
          $startingIndex = $index - 1;
          $size = 2;
          break;
      }
      // transform operator and operands into one expression
      $expression = $operator->transform($leftExpression, $rightExpression);
      // insert OperatorExpression replacing original content
      array_splice($expressionsAndOperators, $startingIndex, $size, [$expression]);
    }
    if(count($expressionsAndOperators) !== 1) {
      throw new ParsingException(ParsingException::ERROR_INVALID_OPERATOR_USE, $nextToken);
    }
    return $expressionsAndOperators[0];
  }
}
