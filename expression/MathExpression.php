<?php
namespace socialistFork\formula\expression;

use socialistFork\formula\ExpressionNotFoundException;
use socialistFork\formula\Nestable;
use socialistFork\formula\ParsingException;
use socialistFork\formula\operator\Calculateable;
use socialistFork\formula\operator\Multiplication;
use socialistFork\formula\operator\Operator;
use socialistFork\formula\operator\Subtraction;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class MathExpression implements Expression, Nestable {

  /**
   * Array containing all expressions and operators that make up this formula
   *
   * @var array<Expression|Operator>
   */
  protected array $expressionsAndOperators = [];

  /**
   * Will be set to true after succsessfull parsing so parsing only needs to occour once
   *
   * @var boolean
   */
  private bool $parsingDone = false;

  /**
   * True if validation has been completed succsessfully
   *
   * @var boolean
   */
  private bool $validated = false;

  /**
   * Primary parsing function
   * Will parse this formula, create and parse all subformulas
   *
   * @inheritdoc
   */
  public function parse(array &$tokens, int &$index): bool {
    if($this->parsingDone) return true;
    $this->expressionsAndOperators = [];
    for($index;$index < sizeof($tokens);$index++) {
      $token = $tokens[$index];
      //       echo "top level:".$this->topLevel.", token: ".$token["name"].", index: $index".PHP_EOL;
      switch($token->name) {
        case ')': // end of this formula if nested
        case ',': // end of this formula if nested
        case ':': // Ternary delimiter
          $this->parsingDone = true;
          return true;
        case '?': // Ternary delimiter
          $this->parseTernary($tokens, $index);
          $this->parsingDone = true;
          return true;
        case 'B': // Boolean
          $this->expressionsAndOperators[] = new BooleanExpression(strtolower($token->value) == "true");
          break;
        case 'O': // Operator
          $this->expressionsAndOperators[] = Operator::fromString($token->value);
          break;
        case 'S': // String literal
          $this->expressionsAndOperators[] = StringLiteral::fromToken($token);
          break;
        case 'N': // number
          if(str_contains($token->value, "%")) {
            $this->expressionsAndOperators[] = new Percent($token->value);
          } else {
            $this->expressionsAndOperators[] = new Number($token->value);
          }
          break;
        case '(': // must be start of new formula
          $expression = new MathExpression();
          $index++;
          if($index >= sizeof($tokens)) throw new ExpressionNotFoundException("Unexpected end of input");
          $expression->parse($tokens, $index); // will throw on failure
          if($index >= sizeof($tokens)) throw new ExpressionNotFoundException("Unexpected end of input");
          if($tokens[$index]->name != ")") throw new ParsingException("", $token);
          $this->expressionsAndOperators[] = $expression;
          break;
        case 'I': // either variable or method
          $variable = new Variable();
          $method = new Method();
          if($variable->parse($tokens, $index)) {
            $this->expressionsAndOperators[] = $variable;
          } else if($method->parse($tokens, $index)) {
            $this->expressionsAndOperators[] = $method;
          } else {
            throw new ParsingException("", $token);
          }
          $index--;
          break;
      }
    }
    $this->parsingDone = true;
    return true;
  }

  private function parseTernary(array &$tokens, int &$index): void {
    $ternaryExpression = new TernaryExpression();
    // clone this MathExpression and complete its parsing
    $condition = new MathExpression();
    $condition->expressionsAndOperators = $this->expressionsAndOperators;
    $condition->parsingDone = true;
    
    $index++;
    if(sizeof($tokens) <= $index) throw new ParsingException("Unexpected end of input", $tokens[$index - 1]);
    // left expression
    $leftExpression = new MathExpression();
    $leftExpression->parse($tokens, $index);
    if(sizeof($tokens) <= $index) throw new ParsingException("Unexpected end of input", $tokens[$index - 1]);
    if($tokens[$index]->name != ":") throw new ParsingException("Expected \":\" (Ternary)", $tokens[$index - 1]);
    $index++;
    if(sizeof($tokens) <= $index) throw new ParsingException("Unexpected end of input", $tokens[$index - 1]);
    // right expression
    $rightExpression = new MathExpression();
    $rightExpression->parse($tokens, $index);
    
    $ternaryExpression->condition = $condition;
    $ternaryExpression->leftExpression = $leftExpression;
    $ternaryExpression->rightExpression = $rightExpression;
    
    // replace this MathExpressions content by the ternary Operator
    $this->expressionsAndOperators = [$ternaryExpression];
  }
  
  /**
   * Will set all variables with the given identifier to a value
   *
   * @param string $identifier the variable name
   * @param float $value
   */
  public function setVariable(string $identifier, $value): void {
    foreach($this->expressionsAndOperators as $expressionOrOperator) {
      if($expressionOrOperator instanceof Nestable) {
        $expressionOrOperator->setVariable($identifier, $value);
      }
    }
  }

  /**
   * Will set all methods with this identifier
   *
   * @param string $identifier
   */
  public function setMethod(string $identifier, callable $method): void {
    foreach($this->expressionsAndOperators as $expressionOrOperator) {
      if($expressionOrOperator instanceof Nestable) {
        $expressionOrOperator->setMethod($identifier, $method);
      }
    }
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
   * Validates that Operators and expressions are in alternating order
   * Will try to insert multiplication operators betweeen Expressions if they are not both a Number
   *
   * @param bool $throwOnError
   * @throws ExpressionNotFoundException if $throwOnError is true and an error is found
   * @return bool is valid
   */
  public function validate(bool $throwOnError): bool {
    if($this->validated) return true;
    if(sizeof($this->expressionsAndOperators) == 0) return true;
    if(sizeof($this->expressionsAndOperators) == 1) {
      if(!($this->expressionsAndOperators[0] instanceof Expression)) {
        $firstOperator = $this->expressionsAndOperators[0];
        if($firstOperator->doesNeedsLeft()) {        
          if($throwOnError) throw new ExpressionNotFoundException("Cant start an expression with an operator");
          return false;
        }
      }
      if($this->expressionsAndOperators[0] instanceof Nestable) {
        return $this->expressionsAndOperators[0]->validate($throwOnError);
      }
    }
    // check if minus is leading. if so prepend 0
    if($this->expressionsAndOperators[0] instanceof Subtraction) { // first is subtraction
      if($this->expressionsAndOperators[1] instanceof Number) { // seconds is number
        $number = $this->expressionsAndOperators[1];
        array_splice($this->expressionsAndOperators, 0, 2, [new Number(-$number->getValue())]); // remove subtraction insert new negative Number
      } else {
        array_splice($this->expressionsAndOperators, 0, 0, [new Number(0)]); // just add 0 because there was no number following
      }
    }
    
    $expectExpression = true;
    for($i = 0;$i < sizeof($this->expressionsAndOperators);$i++) {
      $expressionOrOperator = $this->expressionsAndOperators[$i];
      if($expressionOrOperator instanceof Expression && !$expectExpression) {
        // try inserting multiplication
        if(!(($expressionOrOperator instanceof Number) && ($this->expressionsAndOperators[$i - 1] instanceof Number))) { // not (this one and last one are a number)
          array_splice($this->expressionsAndOperators, $i, 0, [ new Multiplication() ]);
        } else {
          if($throwOnError) throw new ExpressionNotFoundException("Can't chain expressions without operators in between!");
          return false;
        }
      }
      if($expressionOrOperator instanceof Operator && $expectExpression) {
        if($throwOnError) throw new ExpressionNotFoundException("Can't chain operators without expressions in between!");
        return false;
      }
      if($expressionOrOperator instanceof Nestable) {
        if(!$expressionOrOperator->validate($throwOnError)) return false;
      }
      $expectExpression = !$expectExpression;
    }
    // check if last one is no operator
    if($expectExpression) {
      if($throwOnError) throw new ExpressionNotFoundException("Cant end an expression with an operator");
      return false;
    }
    $this->validated = true;
    return true;
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
   * Find the operator with the highest priority (* or /)
   * calculate this operator based on its neighbours
   * replace the operator and neighbours by the resulting number
   * Call recursive until only one operator or one Expression is left
   *
   * @return mixed
   */
  private function calculateRecursive(): Calculateable {
    if(sizeof($this->expressionsAndOperators) == 0) return new Number(0);
    if(sizeof($this->expressionsAndOperators) == 1) {
      return $this->expressionsAndOperators[0]->calculate();
    }
    if(sizeof($this->expressionsAndOperators) == 3) {
      return $this->expressionsAndOperators[1]->calculate($this->expressionsAndOperators[0]->calculate(), $this->expressionsAndOperators[2]->calculate());
    }
    // find highest priority operator
    $maxPriority = 0;
    $bestOperator = 1; // start with index 1 as this will be the first operator if no better operators are found
    for($i = 0;$i < sizeof($this->expressionsAndOperators);$i++) {
      $expressionsAndOperator = $this->expressionsAndOperators[$i];
      if($expressionsAndOperator instanceof Operator) {
        if($maxPriority < $expressionsAndOperator->getPriority()) {
          $maxPriority = $expressionsAndOperator->getPriority();
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
   * @see \socialistFork\formula\expression\Expression::calculate()
   */
  public function calculate(): Calculateable {
    $expressionsAndOperatorsBackup = $this->getExpressionsAndOperatorsBackup();
    $calculateable = $this->calculateRecursive();
    $this->expressionsAndOperators = $expressionsAndOperatorsBackup;
    return $calculateable;
  }
}

