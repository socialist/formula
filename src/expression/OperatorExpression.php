<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression implements Expression {

  public readonly ?Expression $leftExpression;

  public readonly Operator $operator;

  public readonly ?Expression $rightExpression;

  public function __construct(?Expression $leftExpression, Operator $operator, ?Expression $rightExpression) {
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    $this->rightExpression = $rightExpression;
  }

  public function run(): Value {
    return $this->operator->operate($this->leftExpression, $this->rightExpression);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    if($this->leftExpression !== null) {
      $string .= $this->leftExpression->toString($prettyPrintOptions);
    }
    $string .= $this->operator->toString($prettyPrintOptions);
    if($this->rightExpression !== null) {
      $string .= $this->rightExpression->toString($prettyPrintOptions);
    }
    return $string;
  }

  public function getSubParts(): array {
    $subParts = [];
    if($this->leftExpression !== null) {
      $subParts[] = $this->leftExpression;
    }
    $subParts[] = $this->operator;
    if($this->rightExpression !== null) {
      $subParts[] = $this->rightExpression;
    }
    return $subParts;
  }

  public function validate(Scope $scope): Type {
    $this->operator->validate($scope);
    switch($this->operator->getOperatorType()) {
      case OperatorType::Infix:
        if($this->leftExpression === null) {
          throw new FormulaValidationException('Operator '.$this->operator.' is missing it\'s left operand!');
        }
        if($this->rightExpression === null) {
          throw new FormulaValidationException('Operator '.$this->operator.' is missing it\'s right operand!');
        }
      case OperatorType::Prefix:
        if($this->rightExpression === null) {
          throw new FormulaValidationException('Operator '.$this->operator.' is missing it\'s right operand!');
        }
        if($this->leftExpression !== null) {
          throw new \BadFunctionCallException('Invalid OperatorExpression constructed');
        }
      case OperatorType::Postfix:
        if($this->leftExpression === null) {
          throw new FormulaValidationException('Operator '.$this->operator.' is missing it\'s left operand!');
        }
        if($this->rightExpression !== null) {
          throw new \BadFunctionCallException('Invalid OperatorExpression constructed');
        }
    }
    $leftType = null;
    $rightType = null;
    if($this->leftExpression !== null) {
      $leftType = $this->leftExpression->validate($scope);
    }
    $this->operator->validate($scope);
    if($this->rightExpression !== null) {
      $rightType = $this->rightExpression->validate($scope);
    }
    switch($this->operator->getOperatorType()) {
      case OperatorType::Infix:
        $type = $leftType->getOperatorResultType($this->operator, $rightType);
      case OperatorType::Prefix:
        $type = $leftType->getOperatorResultType($this->operator, null);
      case OperatorType::Postfix:
        $type = $leftType->getOperatorResultType($this->operator, null);
      default:
        throw new \UnexpectedValueException('Invalid operatorType!');
    }
    if($type === null) {
      throw new FormulaValidationException('Invalid operator type');
    }
    return $type;
  }
}
