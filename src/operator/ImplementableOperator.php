<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;

/**
 * @author Timo Lehnertz
 *
 *         Represents an operators that can be implemented by values
 */
class ImplementableOperator implements FormulaPart {

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

  private readonly string $identifier;

  /**
   * @param ImplementableOperator::TYPE_*
   */
  public function __construct(int $id) {
    $this->id = $id;
    $this->operatorType = self::idToOperatorType($id);
    $this->identifier = self::idToIdentifier($id);
  }

  private static function idToOperatorType(int $id): OperatorType {
    switch($id) {
      case ImplementableOperator::TYPE_ADDITION:
      case ImplementableOperator::TYPE_SUBTRACTION:
      case ImplementableOperator::TYPE_MULTIPLICATION:
      case ImplementableOperator::TYPE_DIVISION:
      case ImplementableOperator::TYPE_MODULO:
      case ImplementableOperator::TYPE_EQUALS:
      case ImplementableOperator::TYPE_GREATER:
      case ImplementableOperator::TYPE_LESS:
      case ImplementableOperator::TYPE_LOGICAL_AND:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
      case ImplementableOperator::TYPE_SCOPE_RESOLUTION:
      case ImplementableOperator::TYPE_LOGICAL_XOR:
      case ImplementableOperator::TYPE_INSTANCEOF:
      case ImplementableOperator::TYPE_LOGICAL_OR:
      case ImplementableOperator::TYPE_CALL:
      case ImplementableOperator::TYPE_TYPE_CAST:
      case ImplementableOperator::TYPE_ARRAY_ACCESS:
        return OperatorType::InfixOperator;
      case ImplementableOperator::TYPE_NEW:
      case ImplementableOperator::TYPE_UNARY_PLUS:
      case ImplementableOperator::TYPE_UNARY_MINUS:
      case ImplementableOperator::TYPE_LOGICAL_NOT:
        return OperatorType::PrefixOperator;
      default:
        throw new \UnexpectedValueException('Invalid ImplementableOperator ID '.$id);
    }
  }

  private static function idToIdentifier(int $id): string {
    switch($id) {
      case ImplementableOperator::TYPE_SCOPE_RESOLUTION:
        return '::';
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        return '.';
      case ImplementableOperator::TYPE_UNARY_PLUS:
        return '+';
      case ImplementableOperator::TYPE_UNARY_MINUS:
        return '-';
      case ImplementableOperator::TYPE_LOGICAL_NOT:
        return '!';
      case ImplementableOperator::TYPE_NEW:
        return 'new';
      case ImplementableOperator::TYPE_INSTANCEOF:
        return 'instanceof';
      case ImplementableOperator::TYPE_MULTIPLICATION:
        return '*';
      case ImplementableOperator::TYPE_DIVISION:
        return '/';
      case ImplementableOperator::TYPE_MODULO:
        return '%';
      case ImplementableOperator::TYPE_ADDITION:
        return '+';
      case ImplementableOperator::TYPE_SUBTRACTION:
        return '-';
      case ImplementableOperator::TYPE_GREATER:
        return '>';
      case ImplementableOperator::TYPE_LESS:
        return '<';
      case ImplementableOperator::TYPE_EQUALS:
        return '==';
      case ImplementableOperator::TYPE_LOGICAL_AND:
        return '&&';
      case ImplementableOperator::TYPE_LOGICAL_OR:
        return '||';
      case ImplementableOperator::TYPE_LOGICAL_XOR:
        return '^';
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT:
        return '=';
      case ImplementableOperator::TYPE_DIRECT_ASSIGNMENT_OLD_VAL:
        return '=';
      case ImplementableOperator::TYPE_TYPE_CAST:
        return 'typecast';
      case ImplementableOperator::TYPE_ARRAY_ACCESS:
        return '[]';
      case ImplementableOperator::TYPE_CALL:
        return '()';
      default:
        throw new \UnexpectedValueException('Invalid ImplementableOperator ID '.$id);
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
