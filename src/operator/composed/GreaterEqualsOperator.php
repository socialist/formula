<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator\composed;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\ValidationException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\InfixOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class GreaterEqualsOperator extends InfixOperator {

  public function __construct() {
    parent::__construct(9);
  }

  protected function validateInfixOperation(Type $leftValue, Type $rightType): Type {
    $greaterOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_GREATER);
    $lessType = $leftValue->getOperatorResultType($greaterOperator, $rightType);
    if($lessType === null) {
      throw new ValidationException('Invalid operand '.$rightType->getIdentifier());
    }
    if(!($lessType instanceof BooleanType)) {
      throw new InternalFormulaException('Result type of comparison operator was not booelan');
    }
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $comparisonType = $leftValue->getOperatorResultType($comparisonOperator, $lessType);
    if(!($comparisonType instanceof BooleanType)) {
      throw new InternalFormulaException('Result type of comparison operator was not booelan');
    }
    if($comparisonType === null) {
      throw new ValidationException('Invalid operand for >= operator');
    }
    return new BooleanType();
  }

  protected function operateInfix(Value $leftValue, Value $rightValue): Value {
    $greaterOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_GREATER);
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $greaterResult = $leftValue->operate($greaterOperator, $rightValue);
    if($greaterResult->isTruthy()) {
      return new BooleanValue(true);
    } else {
      return new BooleanValue($leftValue->operate($comparisonOperator, $rightValue)->isTruthy());
    }
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '>=';
  }

  public function validate(Scope $scope): void {}
}
