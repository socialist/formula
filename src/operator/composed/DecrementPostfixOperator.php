<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator\composed;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\PrefixOperator;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class DecrementPostfixOperator extends PrefixOperator {

  public function __construct() {
    parent::__construct(2, Operator::PARSABLE_DECREMENT_POSTFIX);
  }

  protected function validatePrefixOperation(Type $rightType): Type {
    $additionOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_SUBTRACTION);
    $assignmentOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT);
    $incrementedType = $rightType->getOperatorResultType($additionOperator, $rightType);
    if($incrementedType === null) {
      throw new FormulaValidationException('Can\'t increment '.$rightType->getIdentifier());
    }
    $finalType = $rightType->getOperatorResultType($assignmentOperator, $incrementedType);
    if($finalType === null) {
      throw new FormulaValidationException('Can\'t assign '.$rightType->getIdentifier().' with '.$incrementedType->getIdentifier());
    }
    return $finalType;
  }

  protected function operatePrefix(Value $rightValue): Value {
    $result = $rightValue->copy();
    $additionOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_SUBTRACTION);
    $assignmentOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT);
    $additionResult = $rightValue->operate($additionOperator, new IntegerValue(1));
    $rightValue->operate($assignmentOperator, $additionResult);
    return $result;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '--';
  }
}
