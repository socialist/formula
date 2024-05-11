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
  public const TYPE_EQUALS = 11;
  public const TYPE_NOT_EQUAL = 12;
  public const TYPE_GREATER = 13;
  public const TYPE_LESS = 14;
  public const TYPE_GREATER_EQUALS = 15;
  public const TYPE_LESS_EQUALS = 16;
  public const TYPE_LOGICAL_AND = 18;
  public const TYPE_LOGICAL_OR = 19;
  public const TYPE_DIRECT_ASIGNMENT = 20;
  public const TYPE_ADDITION_ASIGNMENT = 21;
  public const TYPE_SUBTRACTION_ASIGNMENT = 22;
  public const TYPE_MULTIPLICATION_ASIGNMENT = 23;
  public const TYPE_DIVISION_ASIGNMENT = 24;
  public const TYPE_MODULO_ASIGNMENT = 25;
  public const TYPE_MEMBER_ACCSESS = 26; // a.b
  public const TYPE_SCOPE_RESOLUTION = 28; // ::
  public const TYPE_LOGICAL_NOT = 29;
  public const TYPE_LOGICAL_XOR = 30;
  public const TYPE_AND_ASIGNMENT = 31;
  public const TYPE_OR_ASIGNMENT = 32;
  public const TYPE_XOR_ASIGNMENT = 33;
  public const TYPE_INSTANCEOF = 34;
  public const TYPE_NEW = 35;

  private static array $setups = [
    SimpleOperator::TYPE_SCOPE_RESOLUTION => ["::",OperatorType::Infix,1,true],

    SimpleOperator::TYPE_INCREMENT_POSTFIX        => ["++",OperatorType::Postfix,2,true],
    SimpleOperator::TYPE_DECREMENT_POSTFIX        => ["--",OperatorType::Postfix,2,true],
    SimpleOperator::TYPE_MEMBER_ACCSESS           => [".",OperatorType::Infix,2,true],

    SimpleOperator::TYPE_INCREMENT_PREFIX         => ["++",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_DECREMENT_PREFIX         => ["--",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_UNARY_PLUS               => ["+",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_UNARY_MINUS              => ["-",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_LOGICAL_NOT              => ["!",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_NEW                      => ["new",OperatorType::Prefix,3,false],
    SimpleOperator::TYPE_INSTANCEOF               => ["instanceof",OperatorType::Infix,3,false],

    SimpleOperator::TYPE_MULTIPLICATION           => ["*",OperatorType::Infix,5,true],
    SimpleOperator::TYPE_DIVISION                 => ["/",OperatorType::Infix,5,true],
    SimpleOperator::TYPE_MODULO                   => ["%",OperatorType::Infix,5,true],

    SimpleOperator::TYPE_ADDITION                 => ["+",OperatorType::Infix,6,true],
    SimpleOperator::TYPE_SUBTRACION               => ["-",OperatorType::Infix,6,true],

    SimpleOperator::TYPE_LESS                     => ["<",OperatorType::Infix,9,true],
    SimpleOperator::TYPE_LESS_EQUALS              => ["<=",OperatorType::Infix,9,true],
    SimpleOperator::TYPE_GREATER                  => [">",OperatorType::Infix,9,true],
    SimpleOperator::TYPE_GREATER_EQUALS           => [">=",OperatorType::Infix,9,true],

    SimpleOperator::TYPE_EQUALS                   => ["==",OperatorType::Infix,10,true],
    SimpleOperator::TYPE_NOT_EQUAL                => ["!=",OperatorType::Infix,10,true],

    SimpleOperator::TYPE_LOGICAL_AND              => ["&&",OperatorType::Infix,14,true],

    SimpleOperator::TYPE_LOGICAL_OR               => ["||",OperatorType::Infix,15,true],
    SimpleOperator::TYPE_LOGICAL_XOR              => ["^",OperatorType::Infix,15,true],

    SimpleOperator::TYPE_DIRECT_ASIGNMENT         => ["=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_ADDITION_ASIGNMENT       => ["+=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_SUBTRACTION_ASIGNMENT    => ["-=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_MULTIPLICATION_ASIGNMENT => ["*=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_DIVISION_ASIGNMENT       => ["/=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_AND_ASIGNMENT            => ["&=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_OR_ASIGNMENT             => ["|=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_XOR_ASIGNMENT            => ["^=",OperatorType::Infix,16,true],
    SimpleOperator::TYPE_MODULO_ASIGNMENT         => ["%=",OperatorType::Infix,16,true],
  ];
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
