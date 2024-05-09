<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\operator\SimpleOperator;
use TimoLehnertz\formula\operator\AmbigiousOperator;

/**
 * @author Timo Lehnertz
 */
class OperatorParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn|int {
    switch($firstToken->id) {
      case Token::SCOPE_RESOLUTION:
        return new SimpleOperator(SimpleOperator::TYPE_SCOPE_RESOLUTION);
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
      case Token::EXCLAMATION_MARK:
        return new AmbigiousOperator($firstToken->id);
    }
  }
}
