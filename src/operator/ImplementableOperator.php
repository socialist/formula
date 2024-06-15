<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use PHPUnit\Framework\Constraint\Operator;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 *
 *         Represents an operators that can be implemented by values
 */
class ImplementableOperator {

  // @formatter:off
  public const TYPE_ADDITION = 0;
  public const TYPE_SUBTRACTION = 1;
  public const TYPE_UNARY_PLUS = 2;
  public const TYPE_UNARY_MINUS = 3;
  public const TYPE_MULTIPLICATION = 4;
  public const TYPE_DIVISION = 5;
  public const TYPE_MODULO = 6;
  public const TYPE_EQUALS = 7;
  public const TYPE_GREATER = 8;
  public const TYPE_LESS = 9;
  public const TYPE_LOGICAL_AND = 10;
  public const TYPE_LOGICAL_OR = 11;
  public const TYPE_DIRECT_ASSIGNMENT = 12;
  public const TYPE_DIRECT_ASSIGNMENT_OLD_VAL = 13;
  public const TYPE_MEMBER_ACCESS = 14; // a.b
  public const TYPE_SCOPE_RESOLUTION = 15; // ::
  public const TYPE_LOGICAL_NOT = 16;
  public const TYPE_LOGICAL_XOR = 17;
  public const TYPE_INSTANCEOF = 18;
  public const TYPE_NEW = 19;
  public const TYPE_ARRAY_ACCESS = 20;
  public const TYPE_CALL = 21;
  public const TYPE_TYPE_CAST = 22;
  // @formatter:on

  /**
   * @var ImplementableOperator::TYPE_*
   */
  private readonly int $id;

  private readonly OperatorType $operatorType;

  /**
   * @param ImplementableOperator::TYPE_*
   */
  public function __construct(int $id) {
    $this->id = $id;
    $this->operatorType = self::idToOperatorType($id);
    //     $this->precedence = self::idToPrecedence($id);
    //     $this->identifier = self::idToIdentifier($id);
  }

  private static function idToOperatorType(int $id): OperatorType {
    switch($id) {
      case Operator::TYPE_ADDITION:
      case Operator::TYPE_SUBTRACTION:
      case Operator::TYPE_MULTIPLICATION:
      case Operator::TYPE_DIVISION:
      case Operator::TYPE_MODULO:
      case Operator::TYPE_EQUALS:
      case Operator::TYPE_GREATER:
      case Operator::TYPE_LESS:
      case Operator::TYPE_LOGICAL_AND:
      case Operator::TYPE_DIRECT_ASSIGNMENT:
      case Operator::TYPE_MEMBER_ACCESS:
      case Operator::TYPE_SCOPE_RESOLUTION:
      case Operator::TYPE_LOGICAL_XOR:
      case Operator::TYPE_INSTANCEOF:
      case Operator::TYPE_LOGICAL_OR:
      case Operator::TYPE_CALL:
      case Operator::TYPE_TYPE_CAST:
      case Operator::TYPE_ARRAY_ACCESS:
        return OperatorType::InfixOperator;
      case Operator::TYPE_NEW:
      case Operator::TYPE_UNARY_PLUS:
      case Operator::TYPE_UNARY_MINUS:
      case Operator::TYPE_LOGICAL_NOT:
        return OperatorType::PrefixOperator;
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  //   private static function idToPrecedence(int $id): int {
  //     switch($id) {
  //       case Operator::TYPE_SCOPE_RESOLUTION:
  //         return 1;
  //       case Operator::TYPE_MEMBER_ACCESS:
  //       case Operator::TYPE_ARRAY_ACCESS:
  //       case Operator::TYPE_CALL:
  //         return 2;
  //       case Operator::TYPE_UNARY_PLUS:
  //       case Operator::TYPE_UNARY_MINUS:
  //       case Operator::TYPE_LOGICAL_NOT:
  //       case Operator::TYPE_NEW:
  //       case Operator::TYPE_INSTANCEOF:
  //       case Operator::TYPE_TYPE_CAST:
  //         return 3;
  //       case Operator::TYPE_MULTIPLICATION:
  //       case Operator::TYPE_DIVISION:
  //       case Operator::TYPE_MODULO:
  //         return 5;
  //       case Operator::TYPE_ADDITION:
  //       case Operator::TYPE_SUBTRACTION:
  //         return 6;
  //       case Operator::TYPE_GREATER:
  //       case Operator::TYPE_LESS:
  //         return 9;
  //       case Operator::TYPE_EQUALS:
  //         return 10;
  //       case Operator::TYPE_LOGICAL_AND:
  //         return 14;
  //       case Operator::TYPE_LOGICAL_OR:
  //       case Operator::TYPE_LOGICAL_XOR:
  //         return 15;
  //       case Operator::TYPE_DIRECT_ASSIGNMENT:
  //         return 16;
  //       default:
  //         throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
  //     }
  //   }

  //   private static function idToIdentifier(int $id): string {
  //     switch($id) {
  //       case Operator::TYPE_SCOPE_RESOLUTION:
  //         return '::';
  //       case Operator::TYPE_MEMBER_ACCESS:
  //         return '.';
  //       case Operator::TYPE_UNARY_PLUS:
  //         return '+';
  //       case Operator::TYPE_UNARY_MINUS:
  //         return '-';
  //       case Operator::TYPE_LOGICAL_NOT:
  //         return '!';
  //       case Operator::TYPE_NEW:
  //         return 'new';
  //       case Operator::TYPE_INSTANCEOF:
  //         return 'instanceof';
  //       case Operator::TYPE_MULTIPLICATION:
  //         return '*';
  //       case Operator::TYPE_DIVISION:
  //         return '/';
  //       case Operator::TYPE_MODULO:
  //         return '%';
  //       case Operator::TYPE_ADDITION:
  //         return '+';
  //       case Operator::TYPE_SUBTRACTION:
  //         return '-';
  //       case Operator::TYPE_GREATER:
  //         return '>';
  //       case Operator::TYPE_LESS:
  //         return '<';
  //       case Operator::TYPE_EQUALS:
  //         return '==';
  //       case Operator::TYPE_LOGICAL_AND:
  //         return '&&';
  //       case Operator::TYPE_LOGICAL_OR:
  //         return '||';
  //       case Operator::TYPE_LOGICAL_XOR:
  //         return '^';
  //       case Operator::TYPE_DIRECT_ASSIGNMENT:
  //         return '=';
  //       case Operator::TYPE_TYPE_CAST:
  //         return 'typecast';
  //       case Operator::TYPE_ARRAY_ACCESS:
  //         return '[]';
  //       case Operator::TYPE_CALL:
  //         return '()';
  //       default:
  //         throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
  //     }
  //   }
  public function validateOperation(?Type $leftType, ?Type $rigthType): Type {
    switch($this->operatorType) {
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
    switch($this->operatorType) {
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

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getID(): int {
    return $this->id;
  }
}
