<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\operator\composed\ChainedAssignmentOperator;
use TimoLehnertz\formula\operator\composed\DecrementPostfixOperator;
use TimoLehnertz\formula\operator\composed\DecrementPrefixOperator;
use TimoLehnertz\formula\operator\composed\GreaterEqualsOperator;
use TimoLehnertz\formula\operator\composed\IncrementPostfixOperator;
use TimoLehnertz\formula\operator\composed\IncrementPrefixOperator;
use TimoLehnertz\formula\operator\composed\LessEqualsOperator;
use TimoLehnertz\formula\operator\composed\NotEqualsOperator;

/**
 * @author Timo Lehnertz
 */
class OperatorBuilder {

  public static function buildOperator(int $operatorID): Operator {
    switch($operatorID) {
      case Operator::IMPLEMENTABLE_ADDITION:
      case Operator::IMPLEMENTABLE_SUBTRACTION:
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
      case Operator::IMPLEMENTABLE_DIVISION:
      case Operator::IMPLEMENTABLE_MODULO:
      case Operator::IMPLEMENTABLE_EQUALS:
      case Operator::IMPLEMENTABLE_GREATER:
      case Operator::IMPLEMENTABLE_LESS:
      case Operator::IMPLEMENTABLE_LOGICAL_AND:
      case Operator::IMPLEMENTABLE_LOGICAL_OR:
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
      case Operator::IMPLEMENTABLE_MEMBER_ACCESS:
      case Operator::IMPLEMENTABLE_SCOPE_RESOLUTION:
      case Operator::IMPLEMENTABLE_LOGICAL_NOT:
      case Operator::IMPLEMENTABLE_LOGICAL_XOR:
      case Operator::IMPLEMENTABLE_INSTANCEOF:
      case Operator::IMPLEMENTABLE_NEW:
        return new ImplementableOperator($operatorID);
      case Operator::PARSABLE_INCREMENT_PREFIX:
        return new IncrementPrefixOperator();
      case Operator::PARSABLE_DECREMENT_PREFIX:
        return new DecrementPrefixOperator();
      case Operator::PARSABLE_INCREMENT_POSTFIX:
        return new IncrementPostfixOperator();
      case Operator::PARSABLE_DECREMENT_POSTFIX:
        return new DecrementPostfixOperator();
      case Operator::PARSABLE_LESS_EQUALS:
        return new LessEqualsOperator();
      case Operator::PARSABLE_GREATER_EQUALS:
        return new GreaterEqualsOperator();
      case Operator::PARSABLE_NOT_EQUAL:
        return new NotEqualsOperator();
      case Operator::PARSABLE_ADDITION_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_ADDITION), '+=');
      case Operator::PARSABLE_SUBTRACTION_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_SUBTRACTION), '-=');
      case Operator::PARSABLE_MULTIPLICATION_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_MULTIPLICATION), '*=');
      case Operator::PARSABLE_DIVISION_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_DIVISION), '/=');
      case Operator::PARSABLE_AND_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_LOGICAL_AND), '&=');
      case Operator::PARSABLE_OR_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_LOGICAL_OR), '|=');
      case Operator::PARSABLE_XOR_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_LOGICAL_XOR), '^=');
      case Operator::PARSABLE_MODULO_ASSIGNMENT:
        return new ChainedAssignmentOperator(new ImplementableOperator(Operator::IMPLEMENTABLE_MODULO), '%=');
      default:
        throw new \UnexpectedValueException('Invalid OperatorID '.$operatorID);
    }
  }
}
