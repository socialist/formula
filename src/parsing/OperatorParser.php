<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\operator\SimpleOperator;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\ParsingException;

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
  protected function parsePart(Token $firstToken): ParserReturn|int {
    switch($firstToken->id) {
      case Token::SCOPE_RESOLUTION:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_SCOPE_RESOLUTION), $firstToken->next());
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
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_MEMBER_ACCSESS), $firstToken->next());
      case Token::INCREMENT:
      case Token::DECREMENT:
        /**
         * If the next token is an identifier this must be a prefix as in a++
         * It doesnt work the other way arround as a()++ could also be a legal postfix
         */
        $isPrefix = $firstToken->hasNext() && $firstToken->next()->id === Token::IDENTIFIER;
        if($firstToken->id === Token::INCREMENT) {
          $operatorID = $isPrefix ? SimpleOperator::TYPE_INCREMENT_PREFIX : SimpleOperator::TYPE_INCREMENT_POSTFIX;
        } else {
          $operatorID = $isPrefix ? SimpleOperator::TYPE_DECREMENT_PREFIX : SimpleOperator::TYPE_DECREMENT_POSTFIX;
        }
        return new ParserReturn(new SimpleOperator($operatorID), $firstToken->next());
      case Token::PLUS:
      case Token::MINUS:
        $tokenBefore = $firstToken->prev();
        if($tokenBefore === null) {
          return new ParserReturn(new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_UNARY_PLUS : SimpleOperator::TYPE_UNARY_MINUS), $firstToken->next());
        } else if(isset(static::$inFrontOfUnary[$tokenBefore->id])) {
          return new ParserReturn(new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_UNARY_PLUS : SimpleOperator::TYPE_UNARY_MINUS), $firstToken->next());
        }
        // normal plus
        return new ParserReturn(new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_ADDITION : SimpleOperator::TYPE_SUBTRACION), $firstToken->next());
      case Token::EXCLAMATION_MARK:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LOGICAL_NOT), $firstToken->next());
      case Token::MULTIPLY:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_MULTIPLICATION), $firstToken->next());
      case Token::DIVIDE:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_DIVISION), $firstToken->next());
      case Token::MODULO:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_MODULO), $firstToken->next());
      case Token::COMPARISON_SMALLER:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LESS), $firstToken->next());
      case Token::COMPARISON_GREATER:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_GREATER), $firstToken->next());
      case Token::COMPARISON_SMALLER_EQUALS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LESS_EQUALS), $firstToken->next());
      case Token::COMPARISON_GREATER_EQUALS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_GREATER_EQUALS), $firstToken->next());
      case Token::COMPARISON_EQUALS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_EQUALS), $firstToken->next());
      case Token::COMPARISON_NOT_EQUALS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_NOT_EQUAL), $firstToken->next());
      case Token::LOGICAL_AND:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LOGICAL_AND), $firstToken->next());
      case Token::LOGICAL_OR:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LOGICAL_OR), $firstToken->next());
      case Token::LOGICAL_XOR:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_LOGICAL_XOR), $firstToken->next());
      case Token::ASSIGNMENT:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_DIRECT_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_AND:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_AND_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_DIVIDE:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_DIVISION_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_MINUS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_SUBTRACTION_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_MULTIPLY:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_MULTIPLICATION_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_OR:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_OR_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_PLUS:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_ADDITION_ASIGNMENT), $firstToken->next());
      case Token::ASSIGNMENT_XOR:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_XOR_ASIGNMENT), $firstToken->next());
      case Token::KEYWORD_INSTANCEOF:
        return new ParserReturn(new SimpleOperator(SimpleOperator::TYPE_INSTANCEOF), $firstToken->next());
      default:
        return ParsingException::PARSING_ERROR_GENERIC;
    }
  }
}
