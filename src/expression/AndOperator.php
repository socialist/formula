<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;
use src\ValidationException;
use src\procedure\BooleanType;
use src\procedure\TypeMissmatchException;
use TimoLehnertz\formula\procedure\Value;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class AndOperator extends Operator {

  public function doCalculate(?ReturnValue $left, ?ReturnValue $right): ReturnValue {
    if($left === null || $left->getValue()->getType() instanceof BooleanType) throw new TypeMissmatchException('Invalid type for && operator');
    if($right === null || $right->getValue()->getType() instanceof BooleanType) throw new TypeMissmatchException('Invalid type for && operator');
    return new ReturnValue(new Value(new BooleanType(false), $left->getValue()->getValue() && $right->getValue()->getValue()), null);
  }

  public function validate(Scope $scope, ?Expression $leftExpression, ?Expression $rightExpression, array $exceptions): Type {
    if($leftExpression === null) {
      $exceptions []= new ValidationException('');
      return new BooleanType();
    }
    if($rightExpression === null) {
      $exceptions []= new ValidationException('');
      return new BooleanType();
    }
    if(!($leftExpression->validate($scope) instanceof BooleanExpression && $rightExpression->validate($scope) instanceof BooleanExpression)) {
      $exceptions []= new ValidationException('&& operator only accepts booleans');
    }
    return new BooleanType(false);
  }
}

