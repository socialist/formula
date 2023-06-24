<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\expression\MathExpression;
use TimoLehnertz\formula\expression\Vector;

class ArrayOperator extends Operator implements Parseable, Nestable, SubFormula {

  private MathExpression $indexExpression;
  
  public function __construct() {
    parent::__construct(null, 2, false, true, false, true, false);
  }
  
  /**
   * 
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\Parseable::parse()
   */
  public function parse(array &$tokens, int &$index): bool {
    if($tokens[$index]->value != "[") return false;
    if(sizeof($tokens) < $index + 3) throw new ExpressionNotFoundException("Invalid array operator", $tokens, $index);
    $index++;
    $this->indexExpression = new MathExpression();
    $this->indexExpression->parse($tokens, $index); // will throw on error
    if($tokens[$index]->value != "]") throw new ExpressionNotFoundException("Invalid array operator", $tokens, $index);
    $index++;
    return true;
  }

  /**
   * 
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    if(!($left instanceof Vector)) throw new ExpressionNotFoundException("Cant access array index of ".get_class($left));
    $index = $this->indexExpression->calculate()->getValue();
    if(!is_numeric($index) || is_string($index)) throw new ExpressionNotFoundException($index." Is no valid array index");
    $index = intVal($index);
    return $left->getElement($index)->calculate();
  }
  
  public function getContent(): array {
    if($this->indexExpression instanceof Nestable) {      
      return $this->indexExpression->getContent();
    }
    return $this->indexExpression;
  }

  public function validate(bool $throwOnError): bool {
    return $this->indexExpression->validate($throwOnError);
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\operator\Operator::toString()
   */
  public function toString(): string {
    return '['.$this->indexExpression->toString().']';
  }
}