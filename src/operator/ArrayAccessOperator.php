<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use SebastianBergmann\Type\VoidType;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 */
class ArrayAccessOperator extends PostfixOperator {

  private Expression $indexExpression;

  private ?Type $indexType = null;

  private ?Scope $scope = null;

  public function __construct(Expression $indexExpression) {
    parent::__construct(2);
    $this->indexExpression = $indexExpression;
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return '['.$this->indexExpression->toString($prettyPrintOptions).']';
  }

  public function getIndexType(): Type {
    if($this->indexType === null) {
      throw new \BadFunctionCallException('Validate first');
    }
    return $this->indexType;
  }

  protected function validatePostfixOperation(Type $leftType): Type {
    $operands = $leftType->getCompatibleOperands($this);
    $operandType = null;
    /** @var Type $operand */
    foreach($operands as $operand) {
      if($operand->equals($this->indexType)) {
        $operandType = $this->indexType;
        break;
      }
    }
    if($operandType === null) {
      $possibleCastTypes = $this->indexType->getCompatibleOperands(new TypeCastOperator(false, new VoidType()));
      foreach($operands as $operand) {
        if($operand->equals($possibleCastTypes)) {
          // create cast
          $typeCastOperator = new TypeCastOperator(false, $possibleCastTypes);
          $this->indexExpression = new OperatorExpression(null, $typeCastOperator, $this->indexExpression);
          $this->indexExpression->validate($this->scope);
          $operandType = $possibleCastTypes;
          break;
        }
      }
    }
    if($operandType === null) {
      throw new FormulaValidationException('incompatible array key type');
    }
    return $leftType->getOperatorResultType($this, $operandType);
  }

  protected function operatePostfix(Value $leftValue): Value {
    return $leftValue->operate($this, $this->indexExpression->run());
  }

  public function validate(Scope $scope): void {
    $this->indexType = $this->indexExpression->validate($scope);
    $this->scope = $scope;
  }
}
