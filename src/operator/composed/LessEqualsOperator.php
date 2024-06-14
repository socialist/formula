<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator\composed;

use TimoLehnertz\formula\InternalFormulaException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\ValidationException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\InfixOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class LessEqualsOperator extends InfixOperator {

  public function __construct() {
    parent::__construct(9, Operator::PARSABLE_LESS_EQUALS);
  }

  protected function validateInfixOperation(Type $leftType, Type $rightType): Type {
    $lessOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_LESS);
    $lessType = $leftType->getOperatorResultType($lessOperator, $rightType);
    if($lessType === null) {
      throw new ValidationException('Invalid operand '.$rightType->getIdentifier());
    }
    if(!($lessType instanceof BooleanType)) {
      throw new InternalFormulaException('Result type of comparison operator was not booelan');
    }
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $comparisonType = $leftType->getOperatorResultType($comparisonOperator, $rightType);
    if(!($comparisonType instanceof BooleanType)) {
      var_dump($comparisonType);
      var_dump($leftType);
      var_dump($comparisonOperator);
      var_dump($rightType);
      throw new InternalFormulaException('Result type of comparison operator was not booelan');
    }
    if($comparisonType === null) {
      throw new ValidationException('Invalid operand for <= operator');
    }
    return new BooleanType();
  }

  protected function operateInfix(Value $leftValue, Value $rightValue): Value {
    $lessOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_LESS);
    $comparisonOperator = new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS);
    $lessResult = $leftValue->operate($lessOperator, $rightValue);
    if($lessResult->isTruthy()) {
      return new BooleanValue(true);
    } else {
      return new BooleanValue($leftValue->operate($comparisonOperator, $rightValue)->isTruthy());
    }
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '<=';
  }

  /**
   * @param array<Type> $types
   * @return array<Type>
   */
  public static function joinsTypes(array $typesA, array $typesB): array {
    $typesC = [];
    foreach($typesA as $typeA) {
      foreach($typesB as $typeB) {
        if($typeB->equals($typeA)) {
          $typesC[] = $typeA;
          break;
        }
      }
    }
    return $typesC;
  }

  public function getCompatibleOperands(Type $leftType): array {
    $comparisonOperands = $leftType->getCompatibleOperands(new ImplementableOperator(Operator::IMPLEMENTABLE_EQUALS));
    $lessOperands = $leftType->getCompatibleOperands(new ImplementableOperator(Operator::IMPLEMENTABLE_LESS));
    return self::joinsTypes($comparisonOperands, $lessOperands);
  }
}
