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
class NotEqualsOperator extends InfixOperator {

  public function __construct() {
    parent::__construct(10);
  }

  protected function validateInfixOperation(Type $leftValue, Type $rightType): Type {
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $comparisonType = $leftValue->getOperatorResultType($comparisonOperator, $rightType);
    if(!($comparisonType instanceof BooleanType)) {
      throw new InternalFormulaException('Result type of comparison operator was not booelan');
    }
    if($comparisonType === null) {
      throw new ValidationException('cant compare '.$leftValue->getIdentifier().' and '.$rightType->getIdentifier());
    }
    return new BooleanType();
  }

  protected function operateInfix(Value $leftValue, Value $rightValue): Value {
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $comparisonResult = $leftValue->operate($comparisonOperator, $rightValue);
    return new BooleanValue(!$comparisonResult->isTruthy());
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '!=';
  }

  public function validate(Scope $scope): void {}
}
