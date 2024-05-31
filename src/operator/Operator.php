<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
interface Operator extends FormulaPart {

  // @formatter:off
  public const IMPLEMENTABLE_ADDITION = 0;
  public const IMPLEMENTABLE_SUBTRACTION = 1;
  public const IMPLEMENTABLE_UNARY_PLUS = 2;
  public const IMPLEMENTABLE_UNARY_MINUS = 3;
  public const IMPLEMENTABLE_MULTIPLICATION = 4;
  public const IMPLEMENTABLE_DIVISION = 5;
  public const IMPLEMENTABLE_MODULO = 6;
  public const IMPLEMENTABLE_EQUALS = 7;
  public const IMPLEMENTABLE_GREATER = 8;
  public const IMPLEMENTABLE_LESS = 9;
  public const IMPLEMENTABLE_LOGICAL_AND = 10;
  public const IMPLEMENTABLE_LOGICAL_OR = 11;
  public const IMPLEMENTABLE_DIRECT_ASSIGNMENT = 12;
  public const IMPLEMENTABLE_MEMBER_ACCESS = 13; // a.b
  public const IMPLEMENTABLE_SCOPE_RESOLUTION = 14; // ::
  public const IMPLEMENTABLE_LOGICAL_NOT = 15;
  public const IMPLEMENTABLE_LOGICAL_XOR = 16;
  public const IMPLEMENTABLE_INSTANCEOF = 17;
  public const IMPLEMENTABLE_NEW = 18;
  public const IMPLEMENTABLE_ARRAY_ACCESS = 19;
  public const IMPLEMENTABLE_CALL = 20;
  public const IMPLEMENTABLE_TYPE_CAST = 21;

  public const PARSABLE_INCREMENT_PREFIX = 22;
  public const PARSABLE_DECREMENT_PREFIX = 23;
  public const PARSABLE_INCREMENT_POSTFIX = 24;
  public const PARSABLE_DECREMENT_POSTFIX = 25;
  public const PARSABLE_LESS_EQUALS = 26;
  public const PARSABLE_GREATER_EQUALS = 27;
  public const PARSABLE_NOT_EQUAL = 28;
  public const PARSABLE_ADDITION_ASSIGNMENT = 29;
  public const PARSABLE_SUBTRACTION_ASSIGNMENT = 30;
  public const PARSABLE_MULTIPLICATION_ASSIGNMENT = 31;
  public const PARSABLE_DIVISION_ASSIGNMENT = 32;
  public const PARSABLE_AND_ASSIGNMENT = 33;
  public const PARSABLE_OR_ASSIGNMENT = 34;
  public const PARSABLE_XOR_ASSIGNMENT = 35;
  public const PARSABLE_MODULO_ASSIGNMENT = 36;

  // @formatter:on
  public function getPrecedence(): int;

  public function getOperatorType(): OperatorType;

  public function validateOperation(?Type $leftType, ?Type $rigthType): Type;

  public function operate(?Value $leftValue, ?Value $rightValue): Value;
}
