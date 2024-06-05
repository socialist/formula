<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 *        
 *         Operators that can be implemented by values
 */
class ImplementableOperator implements Operator {

  /**
   * * @var Operator::IMPLEMENTABLE_*
   */
  private readonly int $id;

  private readonly OperatorType $operatorType;

  private readonly int $precedence;

  private readonly string $identifier;

  /**
   * @param Operator::IMPLEMENTABLE_* $id
   */
  public function __construct(int $id) {
    $this->id = $id;
    $this->operatorType = self::idToOperatorType($id);
    $this->precedence = self::idToPrecedence($id);
    $this->identifier = self::idToIdentifier($id);
  }

  private static function idToOperatorType(int $id): OperatorType {
    switch($id) {
      case Operator::IMPLEMENTABLE_ADDITION:
      case Operator::IMPLEMENTABLE_SUBTRACTION:
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
      case Operator::IMPLEMENTABLE_DIVISION:
      case Operator::IMPLEMENTABLE_MODULO:
      case Operator::IMPLEMENTABLE_EQUALS:
      case Operator::IMPLEMENTABLE_GREATER:
      case Operator::IMPLEMENTABLE_LESS:
      case Operator::IMPLEMENTABLE_LOGICAL_AND:
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
      case Operator::IMPLEMENTABLE_MEMBER_ACCESS:
      case Operator::IMPLEMENTABLE_SCOPE_RESOLUTION:
      case Operator::IMPLEMENTABLE_LOGICAL_NOT:
      case Operator::IMPLEMENTABLE_LOGICAL_XOR:
      case Operator::IMPLEMENTABLE_INSTANCEOF:
        return OperatorType::InfixOperator;
      case Operator::IMPLEMENTABLE_LOGICAL_OR:
      case Operator::IMPLEMENTABLE_NEW:
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        return OperatorType::PrefixOperator;
      case Operator::IMPLEMENTABLE_CALL:
      case Operator::IMPLEMENTABLE_ARRAY_ACCESS:
        return OperatorType::PostfixOperator;
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  private static function idToPrecedence(int $id): int {
    switch($id) {
      case Operator::IMPLEMENTABLE_SCOPE_RESOLUTION:
        return 1;
      case Operator::IMPLEMENTABLE_MEMBER_ACCESS:
      case Operator::IMPLEMENTABLE_ARRAY_ACCESS:
      case Operator::IMPLEMENTABLE_CALL:
        return 2;
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
      case Operator::IMPLEMENTABLE_LOGICAL_NOT:
      case Operator::IMPLEMENTABLE_NEW:
      case Operator::IMPLEMENTABLE_INSTANCEOF:
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        return 3;
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
      case Operator::IMPLEMENTABLE_DIVISION:
      case Operator::IMPLEMENTABLE_MODULO:
        return 5;
      case Operator::IMPLEMENTABLE_ADDITION:
      case Operator::IMPLEMENTABLE_SUBTRACTION:
        return 6;
      case Operator::IMPLEMENTABLE_GREATER:
      case Operator::IMPLEMENTABLE_LESS:
        return 9;
      case Operator::IMPLEMENTABLE_EQUALS:
        return 10;
      case Operator::IMPLEMENTABLE_LOGICAL_AND:
        return 14;
      case Operator::IMPLEMENTABLE_LOGICAL_OR:
      case Operator::IMPLEMENTABLE_LOGICAL_XOR:
        return 15;
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
        return 16;
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  private static function idToIdentifier(int $id): string {
    switch($id) {
      case Operator::IMPLEMENTABLE_SCOPE_RESOLUTION:
        return '::';
      case Operator::IMPLEMENTABLE_MEMBER_ACCESS:
        return '.';
      case Operator::IMPLEMENTABLE_UNARY_PLUS:
        return '+';
      case Operator::IMPLEMENTABLE_UNARY_MINUS:
        return '-';
      case Operator::IMPLEMENTABLE_LOGICAL_NOT:
        return '!';
      case Operator::IMPLEMENTABLE_NEW:
        return 'new';
      case Operator::IMPLEMENTABLE_INSTANCEOF:
        return 'instanceof';
      case Operator::IMPLEMENTABLE_MULTIPLICATION:
        return '*';
      case Operator::IMPLEMENTABLE_DIVISION:
        return '/';
      case Operator::IMPLEMENTABLE_MODULO:
        return '%';
      case Operator::IMPLEMENTABLE_ADDITION:
        return '+';
      case Operator::IMPLEMENTABLE_SUBTRACTION:
        return '-';
      case Operator::IMPLEMENTABLE_GREATER:
        return '>';
      case Operator::IMPLEMENTABLE_LESS:
        return '<';
      case Operator::IMPLEMENTABLE_EQUALS:
        return '==';
      case Operator::IMPLEMENTABLE_LOGICAL_AND:
        return '&&';
      case Operator::IMPLEMENTABLE_LOGICAL_OR:
        return '||';
      case Operator::IMPLEMENTABLE_LOGICAL_XOR:
        return '^';
      case Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT:
        return '=';
      case Operator::IMPLEMENTABLE_TYPE_CAST:
        return 'typecast';
      case Operator::IMPLEMENTABLE_ARRAY_ACCESS:
        return '[]';
      case Operator::IMPLEMENTABLE_CALL:
        return '()';
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  public function validateOperation(?Type $leftType, ?Type $rigthType): Type {
    $operatorType = $this->operatorType;
    if($this instanceof CoupledOperator) {
      $operatorType = OperatorType::InfixOperator;
    }
    switch($operatorType) {
      case OperatorType::PrefixOperator:
        if($leftType !== null || $rigthType === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $rigthType->getOperatorResultType($this, null);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid prefix operation '.$this->toString(PrettyPrintOptions::buildDefault()).$rigthType->getIdentifier());
        }
        return $resultType;
      case OperatorType::InfixOperator:
        if($leftType === null || $rigthType === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $leftType->getOperatorResultType($this, $rigthType);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid infix operation '.$leftType->getIdentifier().' '.$this->toString(PrettyPrintOptions::buildDefault()).' '.$rigthType->getIdentifier());
        }
        return $resultType;
      case OperatorType::PostOperator:
        if($leftType === null || $rigthType !== null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $leftType->getOperatorResultType($this, null);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid postfix operation '.$leftType->getIdentifier().' '.$this->toString(PrettyPrintOptions::buildDefault()));
        }
        return $resultType;
      default:
        throw new \UnexpectedValueException('Invalid operatorType');
    }
  }

  public function operate(?Value $leftValue, ?Value $rightValue): Value {
    $operatorType = $this->operatorType;
    if($this instanceof CoupledOperator) {
      $operatorType = OperatorType::InfixOperator;
    }
    switch($operatorType) {
      case OperatorType::PrefixOperator:
        if($leftValue !== null || $rightValue === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        return $rightValue->operate($this, $rightValue);
      case OperatorType::InfixOperator:
        if($leftValue === null || $rightValue === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        return $leftValue->operate($this, $rightValue);
      case OperatorType::PostOperator:
        if($leftValue === null || $rightValue !== null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        return $leftValue->operate($this, null);
      default:
        throw new \UnexpectedValueException('Invalid operatorType');
    }
  }

  public function getOperatorType(): OperatorType {
    return $this->operatorType;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getID(): int {
    return $this->id;
  }
}
