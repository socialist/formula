<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use src\operator\OperatorType;

/**
 *
 * @author Timo Lehnertz
 *        
 */
abstract class Operator implements FormulaPart {

  public const ADDITION = 0;

  public const SUBTRACION = 1;

  public const UNARY_PLUS = 2;

  public const UNARY_MINUS = 3;

  public const MULTIPLICATION = 4;

  public const DIVISION = 5;

  public const MODULO = 6;

  public const INCREMENT_PREFIX = 7;

  public const INCREMENT_POSTFIX = 8;

  public const DECREMENT_PREFIX = 9;

  public const DECREMENT_POSTFIX = 10;

  public const EQUAL = 11;

  public const NOT_EQUAL = 12;

  public const GREATER = 13;

  public const LESS = 14;

  public const GREATER_EQUALS = 15;

  public const LESS_EQUALS = 16;

  public const LOGICAL_NEGOTIATION = 17;

  public const LOGICAL_AND = 18;

  public const LOGICAL_OR = 19;

  public const DIRECT_ASIGNMENT = 20;

  public const ADDITION_ASIGNMENT = 21;

  public const SUBTRACTION_ASIGNMENT = 22;

  public const MULTIPLICATION_ASIGNMENT = 23;

  public const DIVISION_ASIGNMENT = 24;

  public const MODULO_ASIGNMENT = 25;

  public const OBJECT_REFERENCE = 26;

  public const FUNCTION_CALL = 27;

  public const TYPEOF = 28;

  /**
   * precedence of this operator over other operators, lower is higher priority
   * source https://en.cppreference.com/w/cpp/language/operator_precedence
   */
  private readonly int $precedence;

  private readonly OperatorType $type;

  private readonly string $identifier;

  public function __construct(string $identifier, int $precedence, OperatorType $type) {
    $this->precedence = $precedence;
    $this->type = $type;
    $this->identifier = $identifier;
  }

  public function defineReferences(): void {
    //Operators dont define References 
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function getType(): OperatorType {
    return $this->type;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }
}
