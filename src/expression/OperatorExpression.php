<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression implements Expression {

  public readonly ?Expression $leftExpression;

  public readonly Operator $operator;

  public ?Expression $rightExpression;

  public function __construct(?Expression $leftExpression, Operator $operator, ?Expression $rightExpression) {
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    try {
      $operator->getID();
    } catch(\Error $e) {
      var_dump($operator);
      //       throw $e;
      return;
    }
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST && $leftExpression === null) {
      throw new \BadMethodCallException('Moin');
    }
    $this->rightExpression = $rightExpression;
  }

  public function validate(Scope $scope): Type {
    $leftType = $this->leftExpression?->validate($scope) ?? null;
    $rightType = $this->rightExpression?->validate($scope) ?? null;
    if($this->operator->getOperatorType() === OperatorType::InfixOperator && $leftType !== null && $rightType !== null) {
      $operands = $leftType->getCompatibleOperands($this->operator);
      $found = false;
      foreach($operands as $operand) {
        if($operand->equals($rightType)) {
          $found = true;
          break;
        }
      }
      if(!$found) { // insert type cast
        $castableTypes = $rightType->getCompatibleOperands(new TypeCastOperator(false, new VoidType()));
        $found = false;
        foreach($castableTypes as $castableType) {
          foreach($operands as $operand) {
            if($castableType->getType()->equals($operand)) {
              $typeExpression = new TypeExpression($operand);
              $typeExpression->validate($scope);
              $this->rightExpression = new OperatorExpression($this->rightExpression, new TypeCastOperator(false, $operand), $typeExpression);
              $rightType = $operand;
              $found = true;
              break;
            }
            if($found) {
              break;
            }
          }
        }
        if(!$found) {
          throw new FormulaValidationException('Incompatible operands '.$leftType->getIdentifier().' '.$this->operator->toString(PrettyPrintOptions::buildDefault()).' '.$rightType->getIdentifier());
        }
      }
    }
    return $this->operator->validateOperation($leftType, $rightType);
  }

  public function run(Scope $scope): Value {
    return $this->operator->operate($this->leftExpression?->run($scope) ?? null, $this->rightExpression?->run($scope) ?? null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    if($this->leftExpression !== null && $this->operator->getOperatorType() !== OperatorType::PrefixOperator) {
      $string .= $this->leftExpression->toString($prettyPrintOptions);
    }
    $string .= $this->operator->toString($prettyPrintOptions);
    if($this->rightExpression !== null && $this->operator->getOperatorType() !== OperatorType::PostfixOperator) {
      $string .= $this->rightExpression->toString($prettyPrintOptions);
    }
    return $string;
  }

  public function buildNode(Scope $scope): array {
    if(($this->operator instanceof ImplementableOperator) && $this->operator->id !== Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT) {
      return ['type' => 'Operator','outerType' => $this->validate($scope)->buildNode(),'operator' => $this->operator->getIdentifier(),'leftNode' => $this->leftExpression?->buildNode($scope) ?? null,'rightNode' => $this->rightExpression?->buildNode($scope) ?? null];
    } else {
      throw new \BadMethodCallException('Not implementable operator is not supported by Node system');
    }
  }
}
