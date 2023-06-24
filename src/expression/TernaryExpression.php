<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;

/**
 *
 * @author timo
 *        
 */
class TernaryExpression implements Expression, Nestable, SubFormula {
  
  /**
   * Condition
   * @var Calculateable|null
   */
  public ?MathExpression $condition = null;
  
  /**
   * Left expression
   * @var Calculateable|null
   */
  public ?MathExpression $leftExpression = null;
  
  /**
   * Right expression
   * @var Calculateable|null
   */
  public ?MathExpression $rightExpression = null;
  
  public function calculate(): Calculateable {
    return $this->condition->calculate()->isTruthy() ? $this->leftExpression->calculate() : $this->rightExpression->calculate();
  }
  
  private function getExpressions(): array {
    return [$this->condition, $this->leftExpression, $this->rightExpression];
  }
  
  public function getVariables(): array {
    $variables = [];
    foreach ($this->getExpressions() as $expression) {
      if($expression instanceof Nestable) {
        $nestedVariables = $expression->getVariables();
        foreach ($nestedVariables as $nestedVariable) {
          $variables []= $nestedVariable;
        }
      }
    }
    return $variables;
  }

  public function getContent(): array {
    $list = [];
    if($this->condition !==  null) {
      $list[] = $this->condition;
      if($this->condition instanceof Nestable) {        
        $list = array_merge($list, $this->condition->getContent());
      }
    }
    if($this->leftExpression !==  null) {
      $list[] = $this->leftExpression;
      if($this->leftExpression instanceof Nestable) {
        $list = array_merge($list, $this->leftExpression->getContent());
      }
    }
    if($this->rightExpression !==  null) {
      $list[] = $this->rightExpression;
      if($this->rightExpression instanceof Nestable) {
        $list = array_merge($list, $this->rightExpression->getContent());
      }
    }
    return $list;
  }
  
  public function validate(bool $throwOnError): bool {
    if($this->condition === null || $this->leftExpression === null || $this->rightExpression === null) throw new ExpressionNotFoundException("Incomplete ternary expression");
    if(!$this->condition->validate($throwOnError)) return false;
    if(!$this->leftExpression->validate($throwOnError)) return false;
    if(!$this->rightExpression->validate($throwOnError)) return false;
    return true;
  }

  public function toString(): string {
    return ''.$this->condition->toString().'?'.$this->leftExpression->toString().':'.$this->rightExpression->toString().'';
  }
}