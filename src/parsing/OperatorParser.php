<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use PHPUnit\Framework\Constraint\Operator;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\operator\ImplementableParsedOperator;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\operator\IncrementPrefixOperator;
use TimoLehnertz\formula\operator\IncrementPostfixOperator;
use TimoLehnertz\formula\operator\DecrementPrefixOperator;
use TimoLehnertz\formula\operator\DecrementPostfixOperator;
use TimoLehnertz\formula\operator\LessEqualsOperator;
use TimoLehnertz\formula\operator\GreaterEqualsOperator;
use TimoLehnertz\formula\operator\NotEqualsOperator;
use TimoLehnertz\formula\operator\ChainedAssignmentOperator;

/**
 * @author Timo Lehnertz
 */
class OperatorParser extends Parser {

  // @formatter:off
  private static array $inFrontOfUnary = [ // and type cast
    Token::PLUS => true,
    Token::MINUS => true,
    Token::MULTIPLY => true,
    Token::DIVIDE => true,
    Token::MODULO => true,
    Token::EXCLAMATION_MARK => true,
    Token::COMMA => true,
    Token::BRACKETS_OPEN => true,
    Token::CURLY_BRACKETS_OPEN => true,
    Token::SQUARE_BRACKETS_OPEN => true,
    Token::QUESTIONMARK => true,
    Token::COlON => true,
    Token::INTL_BACKSLASH => true,
    Token::SEMICOLON => true,
    Token::COMPARISON_EQUALS => true,
    Token::COMPARISON_GREATER => true,
    Token::COMPARISON_GREATER_EQUALS => true,
    Token::COMPARISON_NOT_EQUALS => true,
    Token::COMPARISON_SMALLER => true,
    Token::COMPARISON_SMALLER_EQUALS => true,
    Token::ASSIGNMENT => true,
    Token::ASSIGNMENT_AND => true,
    Token::ASSIGNMENT_DIVIDE => true,
    Token::ASSIGNMENT_MULTIPLY => true,
    Token::ASSIGNMENT_OR => true,
    Token::ASSIGNMENT_PLUS => true,
    Token::ASSIGNMENT_MINUS => true,
    Token::ASSIGNMENT_XOR => true,
    Token::LOGICAL_XOR => true,
    Token::LOGICAL_AND => true,
    Token::LOGICAL_OR => true,
    Token::KEYWORD_RETURN => true,
  ];

  // @formatter:on
  public function __construct() {
    parent::__construct('operator');
  }

  /**
   * Operator Precedence reference: https://en.cppreference.com/w/cpp/language/operator_precedence
   */
  protected function parsePart(Token $firstToken): ParserReturn {
    switch($firstToken->id) {
      case Token::SCOPE_RESOLUTION:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION, '::', OperatorType::InfixOperator, 1), $firstToken->next());
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
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_MEMBER_ACCESS, '.', OperatorType::InfixOperator, 2), $firstToken->next());
      case Token::INCREMENT:
      case Token::DECREMENT:
        /**
         * If the next token is an identifier this must be a prefix as in a++
         * It doesnt work the other way arround as a()++ could also be a legal postfix
         */
        $isPrefix = $firstToken->hasNext() && $firstToken->next()->id === Token::IDENTIFIER;
        if($firstToken->id === Token::INCREMENT) {
          return new ParserReturn($isPrefix ? new IncrementPrefixOperator() : new IncrementPostfixOperator(), $firstToken->next());
        } else {
          return new ParserReturn($isPrefix ? new DecrementPrefixOperator() : new DecrementPostfixOperator(), $firstToken->next());
        }
      case Token::PLUS:
      case Token::MINUS:
        $tokenBefore = $firstToken->prev();
        if($tokenBefore === null || isset(static::$inFrontOfUnary[$tokenBefore->id])) {
          $implementableID = $firstToken->id === Token::PLUS ? ImplementableOperator::TYPE_UNARY_PLUS : ImplementableOperator::TYPE_UNARY_MINUS;
          $identifier = $firstToken->id === Token::PLUS ? '+' : '-';
          return new ParserReturn(new ImplementableParsedOperator($implementableID, $identifier, OperatorType::PrefixOperator, 3), $firstToken->next());
        }
        // normal plus
        $implementableID = $firstToken->id === Token::PLUS ? ImplementableOperator::TYPE_ADDITION : ImplementableOperator::TYPE_SUBTRACTION;
        $identifier = $firstToken->id === Token::PLUS ? '+' : '-';
        return new ParserReturn(new ImplementableParsedOperator($implementableID, $identifier, OperatorType::InfixOperator, 6), $firstToken->next());
      case Token::EXCLAMATION_MARK:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_LOGICAL_NOT, '!', OperatorType::PrefixOperator, 3), $firstToken->next());
      case Token::MULTIPLY:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_MULTIPLICATION, '*', OperatorType::InfixOperator, 5), $firstToken->next());
      case Token::DIVIDE:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_DIVISION, '/', OperatorType::InfixOperator, 5), $firstToken->next());
      case Token::MODULO:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_MODULO, '%', OperatorType::InfixOperator, 5), $firstToken->next());
      case Token::COMPARISON_SMALLER:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_LESS, '<', OperatorType::InfixOperator, 9), $firstToken->next());
      case Token::COMPARISON_GREATER:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_GREATER, '>', OperatorType::InfixOperator, 9), $firstToken->next());
      case Token::COMPARISON_SMALLER_EQUALS:
        return new ParserReturn(new LessEqualsOperator(), $firstToken->next());
      case Token::COMPARISON_GREATER_EQUALS:
        return new ParserReturn(new GreaterEqualsOperator(), $firstToken->next());
      case Token::COMPARISON_EQUALS:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_EQUALS, '==', OperatorType::InfixOperator, 10), $firstToken->next());
      case Token::COMPARISON_NOT_EQUALS:
        return new ParserReturn(new NotEqualsOperator(), $firstToken->next());
      case Token::LOGICAL_AND:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_LOGICAL_AND, '&&', OperatorType::InfixOperator, 14), $firstToken->next());
      case Token::LOGICAL_OR:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_LOGICAL_OR, '||', OperatorType::InfixOperator, 15), $firstToken->next());
      case Token::LOGICAL_XOR:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_LOGICAL_XOR, '^', OperatorType::InfixOperator, 12), $firstToken->next());
      case Token::ASSIGNMENT:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_DIRECT_ASSIGNMENT, '=', OperatorType::InfixOperator, 16), $firstToken->next());
      case Token::ASSIGNMENT_AND:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_LOGICAL_AND), 16, '&='), $firstToken->next());
      case Token::ASSIGNMENT_DIVIDE:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_DIVISION), 16, '/='), $firstToken->next());
      case Token::ASSIGNMENT_MINUS:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_SUBTRACTION), 16, '-='), $firstToken->next());
      case Token::ASSIGNMENT_MULTIPLY:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_MULTIPLICATION), 16, '*='), $firstToken->next());
      case Token::ASSIGNMENT_OR:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_LOGICAL_OR), 16, '|='), $firstToken->next());
      case Token::ASSIGNMENT_PLUS:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_ADDITION), 16, '+='), $firstToken->next());
      case Token::ASSIGNMENT_XOR:
        return new ParserReturn(new ChainedAssignmentOperator(new ImplementableOperator(ImplementableOperator::TYPE_LOGICAL_XOR), 16, '^='), $firstToken->next());
      case Token::KEYWORD_INSTANCEOF:
        return new ParserReturn(new ImplementableParsedOperator(ImplementableOperator::TYPE_INSTANCEOF, 'instanceof', OperatorType::InfixOperator, 3), $firstToken->next());
      default:
        throw new ParsingSkippedException();
    }
  }
}
