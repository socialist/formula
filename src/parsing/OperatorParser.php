<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\operator\SimpleOperator;
use TimoLehnertz\formula\operator\AmbigiousOperator;
use TimoLehnertz\formula\operator\Operator;

/**
 *
 * @author Timo Lehnertz
 */
class OperatorParser extends Parser {

  // @formatter:off
  private static array $tokenBeforeUnary = [
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
        return new SimpleOperator(SimpleOperator::TYPE_SCOPE_RESOLUTION);
      case Token::BRACKETS_OPEN:
        return (new CallOperatorParser())->parse($firstToken);
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
        return new SimpleOperator($operatorID);
      case Token::PLUS:
      case Token::MINUS:
        $tokenBefore = $firstToken->prev();
        if($tokenBefore === null) {
          return new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_UNARY_PLUS : SimpleOperator::TYPE_UNARY_MINUS);
        } else if(isset(static::$tokenBeforeUnary[$tokenBefore->id])) {
          return new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_UNARY_PLUS : SimpleOperator::TYPE_UNARY_MINUS);
        }
        // normal plus
        return new SimpleOperator($firstToken->id === Token::PLUS ? SimpleOperator::TYPE_ADDITION : SimpleOperator::TYPE_SUBTRACION);
      case Token::EXCLAMATION_MARK:
        return new SimpleOperator(SimpleOperator::TYPE_LOGICAL_NOT);
    }
  }
}
