<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class SimpleOperator extends Operator {

  // @formatter:off
  public const TYPE_ADDITION = 0;
  public const TYPE_SUBTRACION = 1;
  public const TYPE_UNARY_PLUS = 2;
  public const TYPE_UNARY_MINUS = 3;
  public const TYPE_MULTIPLICATION = 4;
  public const TYPE_DIVISION = 5;
  public const TYPE_MODULO = 6;
  public const TYPE_INCREMENT_PREFIX = 7;
  public const TYPE_INCREMENT_POSTFIX = 8;
  public const TYPE_DECREMENT_PREFIX = 9;
  public const TYPE_DECREMENT_POSTFIX = 10;
  public const TYPE_EQUAL = 11;
  public const TYPE_NOT_EQUAL = 12;
  public const TYPE_GREATER = 13;
  public const TYPE_LESS = 14;
  public const TYPE_GREATER_EQUALS = 15;
  public const TYPE_LESS_EQUALS = 16;
  public const TYPE_LOGICAL_NEGOTIATION = 17;
  public const TYPE_LOGICAL_AND = 18;
  public const TYPE_LOGICAL_OR = 19;
  public const TYPE_DIRECT_ASIGNMENT = 20;
  public const TYPE_ADDITION_ASIGNMENT = 21;
  public const TYPE_SUBTRACTION_ASIGNMENT = 22;
  public const TYPE_MULTIPLICATION_ASIGNMENT = 23;
  public const TYPE_DIVISION_ASIGNMENT = 24;
  public const TYPE_MODULO_ASIGNMENT = 25;
  public const TYPE_OBJECT_REFERENCE = 26;
  public const TYPE_FUNCTION_CALL = 27;
  public const TYPE_TYPEOF = 28;
  public const TYPE_SCOPE_RESOLUTION = 29;

  private static array $setups = [
    SimpleOperator::TYPE_ADDITION       => ["+",OperatorType::Infix,6,true],
    SimpleOperator::TYPE_SUBTRACION     => ["-",OperatorType::Infix,6,true],
    SimpleOperator::TYPE_MULTIPLICATION => ["*",OperatorType::Infix,6,true],
    SimpleOperator::TYPE_DIVISION       => ["/",OperatorType::Infix,5,true]];
  // @formatter:on
  private readonly int $id;

  /**
   * @param SimpleOperator::TYPE_* $id
   * @throws \BadFunctionCallException
   */
  public function __construct(int $id) {
    if(!array_key_exists($id, self::$setups)) {
      throw new \BadFunctionCallException('Invalid operatorID');
    }
    parent::__construct(self::$setups[$id][1], self::$setups[$id][2], self::$setups[$id][3]);
    $this->id = $id;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return self::$setups[$this->id][0];
  }

  public function getSubParts(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    throw new \BadFunctionCallException('Not implemented');
  }
}
