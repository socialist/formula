<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\operator\Multiplication;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\types\Type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class FormulaExpression extends Expression {

  /**
   * Array containing all expressions and operators that make up this formula
   *
   * @var FormulaPart[]
   */
  public array $expressionsAndOperators = [];

  /**
   * True if validation has been completed succsessfully
   *
   * @var boolean
   */
  private bool $validated = false;
  
  private bool $insideBrackets;
  
  public function __construct(array $expressionsAndOperators, bool $insideBrackets) {
    $this->insideBrackets = $insideBrackets;
    $this->expressionsAndOperators = $expressionsAndOperators;
  }

  /**
   * Get the original source string
   *
   * @return string
   */
  public function getSource(): string {
    return $this->source;
  }

  /**
   * Validates this expression and all sub expressions
   *
   * @param bool $throwOnError
   * @throws ExpressionNotFoundException if $throwOnError is true and an error is found
   * @return bool is valid
   */
  public function validate(FormulaSettings $formulaSettings): Type {
    if($this->validated) return true;
    // validate sub expressoins
    foreach ($this->expressionsAndOperators as $expressionsAndOperator) {
      if($expressionsAndOperator instanceof FormulaPart) {
        if(!$expressionsAndOperator->validate($formulaSettings)) return false;
      }
    }
    
    // 0 expressions
    if(sizeof($this->expressionsAndOperators) == 0) {
      throw new ExpressionNotFoundException("Empty expression is not allowed", $this->tokens);
    }

    // remove unnecessary brackets
//     if($this->size() === 1 && $this->expressionsAndOperators[0] instanceof FormulaExpression) {
//       $this->expressionsAndOperators[0]->setInsideBrackets(false);
//     }
//     foreach ($this->expressionsAndOperators as $expressionOrOperator) {
//       if(!($expressionOrOperator instanceof FormulaExpression)) continue;
//       if($expressionOrOperator->size() === 1 && !($expressionOrOperator->expressionsAndOperators[0] instanceof TernaryExpression)) {
//         $expressionOrOperator->setInsideBrackets(false);
//       }
//     }
    
    // one expression
    if(sizeof($this->expressionsAndOperators) == 1) { 
      if($this->expressionsAndOperators[0] instanceof Operator) {
        throw new ExpressionNotFoundException("Single Expression can not be an Operator", $this->tokens);
      }
      if($this->expressionsAndOperators[0] instanceof FormulaExpression) {
        return $this->expressionsAndOperators[0]->validate($formulaSettings);
      }
      // it is a different expression
      return $this->expressionsAndOperators[0]->validate($formulaSettings);
    }
    
    // n expressions
    // group operators that only affect one side with their expression to one new expression
    $leftExpression = null;
    $rightExpression = null;
    for ($i = 0; $i < sizeof($this->expressionsAndOperators); $i++) {
      $expression = $this->expressionsAndOperators[$i];
      $rightExpression = null;
      if($i < sizeof($this->expressionsAndOperators) - 1) {
        $rightExpression = $this->expressionsAndOperators[$i + 1];
      }
      if($expression instanceof Operator) {
        if(!$expression->needsLeft() && ($leftExpression === null || $leftExpression instanceof Operator)) { // example: (-1), 1&&-1
          $mathExpression = new FormulaExpression();
          $mathExpression->expressionsAndOperators = [new NoExpression(), $expression, $rightExpression];
          array_splice($this->expressionsAndOperators, $i, 2, [$mathExpression]);
          $expression = $mathExpression; // to have the correct $leftExpression later on
        } else if(!$expression->needsRight() && ($rightExpression === null || $rightExpression instanceof Operator)) {
          $mathExpression = new FormulaExpression();
          $mathExpression->expressionsAndOperators = [$leftExpression, $expression, new NoExpression()];
          array_splice($this->expressionsAndOperators, $i - 1, 2, [$mathExpression]);
          $i--;
          $expression = $mathExpression; // to have the correct $leftExpression later on
        }
      }
      $leftExpression = $expression;
    }
    // check if all operators have sufficient expressions
    $leftExpression = null;
    $rightExpression = null;
    for ($i = 0; $i < sizeof($this->expressionsAndOperators); $i++) {
      $expression = $this->expressionsAndOperators[$i];
      $rightExpression = null;
      if($i < sizeof($this->expressionsAndOperators) - 1) {
        $rightExpression = $this->expressionsAndOperators[$i + 1];
      }
      if($expression instanceof Operator) {
        if($expression->needsLeft() && !($leftExpression instanceof Expression)) throw new ExpressionNotFoundException($expression::class." needs a lefthand expression", $this->tokens);
        if($expression->needsRight() && !($rightExpression instanceof Expression)) throw new ExpressionNotFoundException($expression::class." needs a righthand expression", $this->tokens);
      }
      $leftExpression = $expression;
    }
    
    // Check that operators and expressions are always alternating. Also validate sub expressions recursivly
    $expectExpression = true;
    for($i = 0;$i < sizeof($this->expressionsAndOperators);$i++) {
      $expressionOrOperator = $this->expressionsAndOperators[$i];
      if($expressionOrOperator instanceof Expression && !$expectExpression) {
        // try inserting multiplication
        if(!(($expressionOrOperator instanceof Number) && ($this->expressionsAndOperators[$i - 1] instanceof Number))) { // not (this one and last one are a number)
          array_splice($this->expressionsAndOperators, $i, 0, [ new Multiplication() ]);
        } else {
          throw new ExpressionNotFoundException("Can't chain expressions without operators in between!", $this->tokens);
        }
      }
      if($expressionOrOperator instanceof Operator && $expectExpression) {
        throw new ExpressionNotFoundException("Can't chain operators without expressions in between!", $this->tokens);
      }
      $expectExpression = !$expectExpression;
    }
    // check if last one is no operator
    if($expectExpression) {
      throw new ExpressionNotFoundException("Cant end an expression with an operator", $this->tokens);
    }
    $this->validated = true;
    return true;
  }
  
  /**
   * Size of expressions and operators
   * @return int
   */
  public function size(): int {
  	return sizeof($this->expressionsAndOperators);
  }

  /**
   * Creates a shallow copy of expressionsAndOperators
   *
   * @return array
   */
  private function getExpressionsAndOperatorsBackup(): array {
    $expressionsAndOperatorsBackup = [];
    foreach($this->expressionsAndOperators as $expressionsAndOperator) {
      $expressionsAndOperatorsBackup[] = $expressionsAndOperator;
    }
    return $expressionsAndOperatorsBackup;
  }

  /**
   * Recursivly calculates this Formula
   * procedure:
   * Find the operator with the lowest precedence
   * calculate this operator based on its neighbours
   * replace the operator and neighbours by the resulting number
   * Call recursive until only one operator or one Expression is left
   *
   * @return mixed
   */
  private function calculateRecursive(): ReturnValue {
    if(sizeof($this->expressionsAndOperators) == 1) {
      return $this->expressionsAndOperators[0]->calculate();
    }
    // find lowest precedence operator
    $minPrecedence = 1000;
    $bestOperator = 1; // start with index 1 as this will be the first operator if no better operators are found
    for($i = 0;$i < sizeof($this->expressionsAndOperators);$i++) {
      $expressionsAndOperator = $this->expressionsAndOperators[$i];
      if($expressionsAndOperator instanceof Operator) {
        if($minPrecedence > $expressionsAndOperator->getPrecedence()) {
          $minPrecedence = $expressionsAndOperator->getPrecedence();
          $bestOperator = $i;
        }
      }
    }
    // execute highes priority operator and replace it and its expressions by the resulting value
    $value = $this->expressionsAndOperators[$bestOperator]->calculate($this->expressionsAndOperators[$bestOperator - 1]->calculate(), $this->expressionsAndOperators[$bestOperator + 1]->calculate());
    // replace Operator and neighbours by the result
    array_splice($this->expressionsAndOperators, $bestOperator - 1, 3, [ $value ]);
    return $this->calculateRecursive();
  }

  /**
   * Will calculate the current value of this formula
   * Can be called multiple times to change variables or functions
   *
   * {@inheritdoc}
   * @see \TimoLehnertz\formula\expression\Expression::calculate()
   */
  public function run(): ReturnValue {
    $expressionsAndOperatorsBackup = $this->getExpressionsAndOperatorsBackup();
    $calculateable = $this->calculateRecursive();
    $this->expressionsAndOperators = $expressionsAndOperatorsBackup;
    return $calculateable;
  }
  
  public function getSubExpressions(): array {
    $content = [];
    foreach ($this->expressionsAndOperators as $expressionOrOperator) {
      $content[] = $expressionOrOperator;
      $content = array_merge($content, $expressionOrOperator->getContent());
    }
    return $content;
  }
  
  public function setInsideBrackets(bool $insideBrackets): void {
    $this->insideBrackets = $insideBrackets;
  }
  
  public function toString(): string {
    $string  = '';
    foreach ($this->expressionsAndOperators as $expressionOrOperator) {
        $string .= $expressionOrOperator->toString();
    }
    if($this->insideBrackets) {
      return "($string)";      
    } else {
      return $string;
    }
  }
}

