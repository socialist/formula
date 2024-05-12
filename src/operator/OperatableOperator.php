<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 *
 *         Operator that can operate on values
 */
abstract class OperatableOperator implements Operator {

  // @formatter:off
  public const TYPE_ADDITION = 0;
  public const TYPE_SUBTRACION = 1;
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
  public const TYPE_DIRECT_ASIGNMENT = 12;
  public const TYPE_MEMBER_ACCSESS = 13; // a.b
  public const TYPE_SCOPE_RESOLUTION = 14; // ::
  public const TYPE_LOGICAL_NOT = 15;
  public const TYPE_LOGICAL_XOR = 16;
  public const TYPE_INSTANCEOF = 17;
  public const TYPE_NEW = 18;
  public const TYPE_ARRAY_ACCESS = 19;
  public const TYPE_CALL = 20;
  public const TYPE_TYPE_CAST = 21;
  // @formatter:on
  public readonly int $id;

  /**
   * precedence of this operator over other operators, lower is higher priority
   * source https://en.cppreference.com/w/cpp/language/operator_precedence
   */
  protected readonly int $precedence;

  public function __construct(int $id, int $precedence) {
    $this->id = $id;
    $this->precedence = $precedence;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public abstract function operate(?Value $leftValue, ?Value $rightValue): Value;

  public abstract function validateOperation(?Type $leftType, ?Type $rigthType): Type;
}
