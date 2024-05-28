<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\OperatorBuilder;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class OperatorParser extends Parser {

  // @formatter:off
  private static array $inFrontOfUnary = [ // and type cast
    // operators
    Token::PLUS => true,
    Token::MINUS => true,
    Token::MULTIPLY => true,
    Token::DIVIDE => true,
    Token::MODULO => true,
    Token::EXCLAMATION_MARK => true,
    // tokens before expressions
    Token::COMMA => true,
    Token::BRACKETS_OPEN => true,
    Token::BRACKETS_CLOSED => true,
    Token::CURLY_BRACKETS_OPEN => true,
    Token::CURLY_BRACKETS_CLOSED => true,
    Token::SQUARE_BRACKETS_OPEN => true,
    Token::SQUARE_BRACKETS_CLOSED => true,
    Token::QUESTIONMARK => true,
    Token::COlON => true,
    Token::INTL_BACKSLASH => true,
    Token::SEMICOLON => true,
  ];

  // @formatter:on
  protected function parsePart(Token $firstToken): ParserReturn {
    switch($firstToken->id) {
      case Token::SCOPE_RESOLUTION:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_SCOPE_RESOLUTION), $firstToken->next());
      case Token::BRACKETS_OPEN:
        $tokenBefore = $firstToken->prev();
        if($tokenBefore === null || isset(static::$inFrontOfUnary[$tokenBefore->id])) {
          return (new TypeCastOperatorParser())->parse($firstToken);
        } else {
          return (new CallOperatorParser())->parse($firstToken);
        }
      case Token::SQUARE_BRACKETS_OPEN:
        return (new ArrayOperatorParser())->parse($firstToken);
      case Token::DOT:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_MEMBER_ACCESS), $firstToken->next());
      case Token::INCREMENT:
      case Token::DECREMENT:
        /**
         * If the next token is an identifier this must be a prefix as in a++
         * It doesnt work the other way arround as a()++ could also be a legal postfix
         */
        $isPrefix = $firstToken->hasNext() && $firstToken->next()->id === Token::IDENTIFIER;
        if($firstToken->id === Token::INCREMENT) {
          $operatorID = $isPrefix ? Operator::PARSABLE_INCREMENT_PREFIX : Operator::PARSABLE_INCREMENT_POSTFIX;
        } else {
          $operatorID = $isPrefix ? Operator::PARSABLE_DECREMENT_PREFIX : Operator::PARSABLE_DECREMENT_POSTFIX;
        }
        return new ParserReturn(OperatorBuilder::buildOperator($operatorID), $firstToken->next());
      case Token::PLUS:
      case Token::MINUS:
        $tokenBefore = $firstToken->prev();
        if($tokenBefore === null) {
          return new ParserReturn(OperatorBuilder::buildOperator($firstToken->id === Token::PLUS ? Operator::IMPLEMENTABLE_UNARY_PLUS : Operator::IMPLEMENTABLE_UNARY_MINUS), $firstToken->next());
        } else if(isset(static::$inFrontOfUnary[$tokenBefore->id])) {
          return new ParserReturn(OperatorBuilder::buildOperator($firstToken->id === Token::PLUS ? Operator::IMPLEMENTABLE_UNARY_PLUS : Operator::IMPLEMENTABLE_UNARY_MINUS), $firstToken->next());
        }
        // normal plus
        return new ParserReturn(OperatorBuilder::buildOperator($firstToken->id === Token::PLUS ? Operator::IMPLEMENTABLE_ADDITION : Operator::IMPLEMENTABLE_SUBTRACTION), $firstToken->next());
      case Token::EXCLAMATION_MARK:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_LOGICAL_NOT), $firstToken->next());
      case Token::MULTIPLY:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_MULTIPLICATION), $firstToken->next());
      case Token::DIVIDE:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_DIVISION), $firstToken->next());
      case Token::MODULO:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_MODULO), $firstToken->next());
      case Token::COMPARISON_SMALLER:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_LESS), $firstToken->next());
      case Token::COMPARISON_GREATER:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_GREATER), $firstToken->next());
      case Token::COMPARISON_SMALLER_EQUALS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_LESS_EQUALS), $firstToken->next());
      case Token::COMPARISON_GREATER_EQUALS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_GREATER_EQUALS), $firstToken->next());
      case Token::COMPARISON_EQUALS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_EQUALS), $firstToken->next());
      case Token::COMPARISON_NOT_EQUALS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_NOT_EQUAL), $firstToken->next());
      case Token::LOGICAL_AND:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_LOGICAL_AND), $firstToken->next());
      case Token::LOGICAL_OR:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_LOGICAL_OR), $firstToken->next());
      case Token::LOGICAL_XOR:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_LOGICAL_XOR), $firstToken->next());
      case Token::ASSIGNMENT:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_AND:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_AND_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_DIVIDE:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_DIVISION_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_MINUS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_SUBTRACTION_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_MULTIPLY:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_MULTIPLICATION_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_OR:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_OR_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_PLUS:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_ADDITION_ASSIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_XOR:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::PARSABLE_XOR_ASSIGNMENT), $firstToken->next());
      case Token::KEYWORD_INSTANCEOF:
        return new ParserReturn(OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_INSTANCEOF), $firstToken->next());
      default:
        throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
  }
}
