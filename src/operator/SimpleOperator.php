<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class SimpleOperator extends Operator {

  // @formatter:off
  private static array $setups = [
    Operator::TYPE_SCOPE_RESOLUTION => ["::",OperatorType::Infix,1,true],

    Operator::TYPE_INCREMENT_POSTFIX        => ["++",OperatorType::Postfix,2,true],
    Operator::TYPE_DECREMENT_POSTFIX        => ["--",OperatorType::Postfix,2,true],
    Operator::TYPE_MEMBER_ACCSESS           => [".",OperatorType::Infix,2,true],

    Operator::TYPE_INCREMENT_PREFIX         => ["++",OperatorType::Prefix,3,false],
    Operator::TYPE_DECREMENT_PREFIX         => ["--",OperatorType::Prefix,3,false],
    Operator::TYPE_UNARY_PLUS               => ["+",OperatorType::Prefix,3,false],
    Operator::TYPE_UNARY_MINUS              => ["-",OperatorType::Prefix,3,false],
    Operator::TYPE_LOGICAL_NOT              => ["!",OperatorType::Prefix,3,false],
    Operator::TYPE_NEW                      => ["new",OperatorType::Prefix,3,false],
    Operator::TYPE_INSTANCEOF               => ["instanceof",OperatorType::Infix,3,false],

    Operator::TYPE_MULTIPLICATION           => ["*",OperatorType::Infix,5,true],
    Operator::TYPE_DIVISION                 => ["/",OperatorType::Infix,5,true],
    Operator::TYPE_MODULO                   => ["%",OperatorType::Infix,5,true],

    Operator::TYPE_ADDITION                 => ["+",OperatorType::Infix,6,true],
    Operator::TYPE_SUBTRACION               => ["-",OperatorType::Infix,6,true],

    Operator::TYPE_LESS                     => ["<",OperatorType::Infix,9,true],
    Operator::TYPE_LESS_EQUALS              => ["<=",OperatorType::Infix,9,true],
    Operator::TYPE_GREATER                  => [">",OperatorType::Infix,9,true],
    Operator::TYPE_GREATER_EQUALS           => [">=",OperatorType::Infix,9,true],

    Operator::TYPE_EQUALS                   => ["==",OperatorType::Infix,10,true],
    Operator::TYPE_NOT_EQUAL                => ["!=",OperatorType::Infix,10,true],

    Operator::TYPE_LOGICAL_AND              => ["&&",OperatorType::Infix,14,true],

    Operator::TYPE_LOGICAL_OR               => ["||",OperatorType::Infix,15,true],
    Operator::TYPE_LOGICAL_XOR              => ["^",OperatorType::Infix,15,true],

    Operator::TYPE_DIRECT_ASIGNMENT         => ["=",OperatorType::Infix,16,true],
    Operator::TYPE_ADDITION_ASIGNMENT       => ["+=",OperatorType::Infix,16,true],
    Operator::TYPE_SUBTRACTION_ASIGNMENT    => ["-=",OperatorType::Infix,16,true],
    Operator::TYPE_MULTIPLICATION_ASIGNMENT => ["*=",OperatorType::Infix,16,true],
    Operator::TYPE_DIVISION_ASIGNMENT       => ["/=",OperatorType::Infix,16,true],
    Operator::TYPE_AND_ASIGNMENT            => ["&=",OperatorType::Infix,16,true],
    Operator::TYPE_OR_ASIGNMENT             => ["|=",OperatorType::Infix,16,true],
    Operator::TYPE_XOR_ASIGNMENT            => ["^=",OperatorType::Infix,16,true],
    Operator::TYPE_MODULO_ASIGNMENT         => ["%=",OperatorType::Infix,16,true],
  ];
  // @formatter:on

  /**
   * @param Operator::TYPE_* $id
   * @throws \BadFunctionCallException
   */
  public function __construct(int $id) {
    if(!array_key_exists($id, self::$setups)) {
      throw new \BadFunctionCallException('Invalid operatorID');
    }
    parent::__construct($id, self::$setups[$id][1], self::$setups[$id][2], self::$setups[$id][3]);
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

  public function operate(?Expression $leftExpression, ?Expression $rightExpression): Value {
    switch($this->operatorType) {
      case OperatorType::Infix:
        if($leftExpression === null || $rightExpression === null) {
          throw new \BadFunctionCallException('Invalid operation');
        }
        return $leftExpression->run()->operate($this, $rightExpression->run());
      case OperatorType::Prefix:
        if($rightExpression === null) {
          throw new \BadFunctionCallException('Invalid operation');
        }
        return $rightExpression->run()->operate($this, null);
      case OperatorType::Postfix:
        if($leftExpression === null) {
          throw new \BadFunctionCallException('Invalid operation');
        }
        return $leftExpression->run()->operate($this, null);
      default:
        throw new \UnexpectedValueException('Invalid operatorType!');
    }
  }
}
