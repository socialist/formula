<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\expression\ArrayExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\FormulaExpression;
use TimoLehnertz\formula\procedure\Scope;
use src\ValidationException;

class ArrayOperator extends Operator {

  private FormulaExpression $indexExpression;
  
  public function __construct(FormulaExpression $indexExpression) {
    parent::__construct(null, 2, false, true, false, true, false);
    $this->indexExpression = $indexExpression;
  }

  /**
   * 
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    if(!($left instanceof ArrayExpression)) throw new ExpressionNotFoundException("Cant access array index of ".get_class($left));
    $index = $this->indexExpression->calculate()->getValue();
    if(!is_numeric($index) || is_string($index)) throw new ExpressionNotFoundException($index." Is no valid array index");
    $index = intVal($index);
    return $left->getElement($index)->calculate();
  }
  
  public function getSubExpressions(): array {
    $arr = [$this->indexExpression];
    foreach($this->indexExpression->getSubExpressions() as $aubExpression) {      
      $arr []= $aubExpression;
    }
    return $arr;
  }

  public function validate(Scope $scope, ?Expression $leftExpression, ?Expression $rightExpression, array $exceptions): bool {
    if($leftExpression === null) {
      $exceptions []= new ValidationException('No Array for Array operator');
    }
    
    return $this->indexExpression->validate($scope, $exceptions);
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\operator\Operator::toString()
   */
  public function toString(): string {
    return '['.$this->indexExpression->toString().']';
  }
}