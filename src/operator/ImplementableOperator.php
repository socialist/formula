<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 *
 *         Operators that can be implemented by values
 */
class ImplementableOperator implements Operator {

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
  public const TYPE_MEMBER_ACCESS = 13; // a.b
  public const TYPE_SCOPE_RESOLUTION = 14; // ::
  public const TYPE_LOGICAL_NOT = 15;
  public const TYPE_LOGICAL_XOR = 16;
  public const TYPE_INSTANCEOF = 17;
  public const TYPE_NEW = 18;
  public const TYPE_ARRAY_ACCESS = 19;
  public const TYPE_CALL = 20;
  public const TYPE_TYPE_CAST = 21;

  // @formatter:on
  /**
   * @var ImplementableOperator::TYPE_*
   */
  public readonly int $id;

  private readonly OperatorType $operatorType;

  private readonly int $precedence;

  private readonly string $identifier;

  /**
   * @param ImplementableOperator::TYPE_* $id
   */
  public function __construct(int $id) {
    $this->id = $id;
    $this->operatorType = self::idToOperatorType($id);
    $this->precedence = self::idToPrecedence($id);
    $this->identifier = self::idToIdentifier($id);
  }

  private static function idToOperatorType(int $id): OperatorType {
    switch($id) {
      case self::TYPE_ADDITION:
      case self::TYPE_SUBTRACTION:
      case self::TYPE_MULTIPLICATION:
      case self::TYPE_DIVISION:
      case self::TYPE_MODULO:
      case self::TYPE_EQUALS:
      case self::TYPE_GREATER:
      case self::TYPE_LESS:
      case self::TYPE_LOGICAL_AND:
      case self::TYPE_DIRECT_ASSIGNMENT:
      case self::TYPE_MEMBER_ACCESS:
      case self::TYPE_SCOPE_RESOLUTION:
      case self::TYPE_LOGICAL_NOT:
      case self::TYPE_LOGICAL_XOR:
      case self::TYPE_INSTANCEOF:
        return OperatorType::InfixOperator;
      case self::TYPE_LOGICAL_OR:
      case self::TYPE_NEW:
      case self::TYPE_UNARY_PLUS:
      case self::TYPE_UNARY_MINUS:
      case self::TYPE_TYPE_CAST:
        return OperatorType::PrefixOperator;
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  private static function idToPrecedence(int $id): int {
    switch($id) {
      case self::TYPE_SCOPE_RESOLUTION:
        return 1;
      case self::TYPE_MEMBER_ACCESS:
        return 2;
      case self::TYPE_UNARY_PLUS:
      case self::TYPE_UNARY_MINUS:
      case self::TYPE_LOGICAL_NOT:
      case self::TYPE_NEW:
      case self::TYPE_INSTANCEOF:
      case self::TYPE_TYPE_CAST:
        return 3;
      case self::TYPE_MULTIPLICATION:
      case self::TYPE_DIVISION:
      case self::TYPE_MODULO:
        return 5;
      case self::TYPE_ADDITION:
      case self::TYPE_SUBTRACTION:
        return 6;
      case self::TYPE_GREATER:
      case self::TYPE_LESS:
        return 9;
      case self::TYPE_EQUALS:
        return 10;
      case self::TYPE_LOGICAL_AND:
        return 14;
      case self::TYPE_LOGICAL_OR:
      case self::TYPE_LOGICAL_XOR:
        return 15;
      case self::TYPE_DIRECT_ASSIGNMENT:
        return 16;
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  private static function idToIdentifier(int $id): string {
    switch($id) {
      case self::TYPE_SCOPE_RESOLUTION:
        return '::';
      case self::TYPE_MEMBER_ACCESS:
        return '.';
      case self::TYPE_UNARY_PLUS:
        return '+';
      case self::TYPE_UNARY_MINUS:
        return '-';
      case self::TYPE_LOGICAL_NOT:
        return '!';
      case self::TYPE_NEW:
        return 'new';
      case self::TYPE_INSTANCEOF:
        return 'instanceof';
      case self::TYPE_MULTIPLICATION:
        return '*';
      case self::TYPE_DIVISION:
        return '/';
      case self::TYPE_MODULO:
        return '%';
      case self::TYPE_ADDITION:
        return '+';
      case self::TYPE_SUBTRACTION:
        return '-';
      case self::TYPE_GREATER:
        return '>';
      case self::TYPE_LESS:
        return '<';
      case self::TYPE_EQUALS:
        return '==';
      case self::TYPE_LOGICAL_AND:
        return '&&';
      case self::TYPE_LOGICAL_OR:
        return '||';
      case self::TYPE_LOGICAL_XOR:
        return '^';
      case self::TYPE_DIRECT_ASSIGNMENT:
        return '=';
      case self::TYPE_TYPE_CAST:
        return 'typecast';
      default:
        throw new \UnexpectedValueException('invalid ImplementableOperator ID '.$id);
    }
  }

  public function validateOperation(?Type $leftType, ?Type $rigthType): Type {
    switch($this->operatorType) {
      case OperatorType::PrefixOperator:
        if($leftType !== null || $rigthType === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $leftType->getOperatorResultType($this, null);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid operation');
        }
        return $resultType;
      case OperatorType::InfixOperator:
        if($leftType === null || $rigthType === null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $leftType->getOperatorResultType($this, $rigthType);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid operation');
        }
        return $resultType;
      case OperatorType::PostOperator:
        if($leftType === null || $rigthType !== null) {
          throw new \UnexpectedValueException('Invalid operands for operator #'.$this->id);
        }
        $resultType = $leftType->getOperatorResultType($this, null);
        if($resultType === null) {
          throw new FormulaValidationException('Invalid operation');
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
        return $rightValue->operate($this, null);
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

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function validate(Scope $scope): void {}
}
