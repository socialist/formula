<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\ArrayOperator;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\operator\Multiplication;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\procedure\Scope;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class MathExpression implements Expression, Nestable, SubFormula {

  /**
   * Array containing all expressions and operators that make up this formula
   *
   * @var SubFormula[]
   */
  public array $expressionsAndOperators = [];

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
   * @var array<Token> array of all tokens
   */
  private array $tokens = [];
  
  private bool $insideBrackets;
  
  public function __construct(bool $insideBrackets = false) {
    $this->insideBrackets = $insideBrackets;
  }
  
  /**
   * Primary parsing function
   * Will parse this formula, create and parse all subformulas
   *
   * @inheritdoc
   */
  public function parse(array &$tokens, int &$index): bool {
    if($this->parsingDone) return true;
    $this->tokens = $tokens;
    $this->expressionsAndOperators = [];
    for($index;$index < sizeof($tokens);$index++) {
      $token = $tokens[$index];
      //       echo "top level:".$this->topLevel.", token: ".$token["name"].", index: $index".PHP_EOL;
      switch($token->name) {
        case ')': // end of this formula if nested
        case ',': // end of this formula if nested
        case ':': // Ternary delimiter
        case '}': // Vector delimiter
        case ',': // Vector element delimiter
          if(sizeof($this->expressionsAndOperators) === 0) {
            throw new ExpressionNotFoundException('Expression can\'t be empty', $tokens, $index);
          }
          $this->parsingDone = true;
          return true;
        case ']': // Array operator end
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
        case 'null': // null
          $this->expressionsAndOperators[] = new NullExpression();
          break;
        case 'N': // number
          if(str_contains($token->value, "%")) {
            $this->expressionsAndOperators[] = new Percent($token->value);
          } else {
            $this->expressionsAndOperators[] = new Number($token->value);
          }
          break;
        case '{': // vector
        	$vector = new Vector();
        	$vector->parse($tokens, $index); // will throw on error
        	$this->expressionsAndOperators[] = $vector;
        	$index--; // prevent $index++
        	break;
        case '[': // Array operator
          $arrayOperator = new ArrayOperator();
          $arrayOperator->parse($tokens, $index); // will throw on error
          $this->expressionsAndOperators[] = $arrayOperator;
          $index--; // prevent $index++
          break;
        case '(': // must be start of new formula
          $expression = new MathExpression(true);
          $index++;
          if($index >= sizeof($tokens)) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
          $expression->parse($tokens, $index); // will throw on failure
          if($index >= sizeof($tokens)) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
          if($tokens[$index]->name != ")") throw new ParsingException("", $token);
          $this->expressionsAndOperators[] = $expression;
          break;
        case 'I': // either variable, method or assignment
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
    if($condition->size() == 0) throw new ExpressionNotFoundException("Invalid ternary condition", $tokens, $index);
    $condition->parsingDone = true;
    
    $index++;
    if(sizeof($tokens) <= $index) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
    // left expression
    $leftExpression = new MathExpression();
    $leftExpression->parse($tokens, $index);
    if($leftExpression->size() == 0) throw new ExpressionNotFoundException("Invalid ternary expression", $tokens, $index);
    if(sizeof($tokens) <= $index) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
    if($tokens[$index]->name != ":") throw new ExpressionNotFoundException("Expected \":\" (Ternary)", $tokens, $index);
    $index++;
    if(sizeof($tokens) <= $index) throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
    // right expression
    $rightExpression = new MathExpression();
    $rightExpression->parse($tokens, $index);
    if($rightExpression->size() == 0) throw new ExpressionNotFoundException("Invalid ternary expression", $tokens, $index);
    
    $ternaryExpression->condition = $condition;
    $ternaryExpression->leftExpression = $leftExpression;
    $ternaryExpression->rightExpression = $rightExpression;
    
    // replace this MathExpressions content by the ternary Operator
    $this->expressionsAndOperators = [$ternaryExpression];
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
  public function validate(bool $throwOnError, Scope $scope): bool {
    if($this->validated) return true;
    // validate sub expressoins
    foreach ($this->expressionsAndOperators as $expressionsAndOperator) {
      if($expressionsAndOperator instanceof Nestable) {
        if(!$expressionsAndOperator->validate($throwOnError)) return false;
      }
    }
    
    // 0 expressions
    if(sizeof($this->expressionsAndOperators) == 0) {
      if($throwOnError) throw new ExpressionNotFoundException("Empty expression is not allowed", $this->tokens);
    }

    // remove unnecessary brackets
    foreach ($this->expressionsAndOperators as $expressionOrOperator) {
      if(!($expressionOrOperator instanceof MathExpression)) continue;
      if($this->size() === 1) {
        $expressionOrOperator->setInsideBrackets(false);
      }
      if($expressionOrOperator->size() === 1 && !($expressionOrOperator->expressionsAndOperators[0] instanceof TernaryExpression)) {
        $expressionOrOperator->setInsideBrackets(false);
      }
    }
    
    // one expression
    if(sizeof($this->expressionsAndOperators) == 1) { 
      if(!($this->expressionsAndOperators[0] instanceof Expression)) {
        if($throwOnError) throw new ExpressionNotFoundException("Single Expression can not be an Operator", $this->tokens);
        return false;
      }
      if($this->expressionsAndOperators[0] instanceof MathExpression) {
        return $this->expressionsAndOperators[0]->validate($throwOnError);
      }
      return true;
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
          $mathExpression = new MathExpression();
          $mathExpression->expressionsAndOperators = [new NoExpression(), $expression, $rightExpression];
          array_splice($this->expressionsAndOperators, $i, 2, [$mathExpression]);
          $expression = $mathExpression; // to have the correct $leftExpression later on
        } else if(!$expression->needsRight() && ($rightExpression === null || $rightExpression instanceof Operator)) {
          $mathExpression = new MathExpression();
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
          if($throwOnError) throw new ExpressionNotFoundException("Can't chain expressions without operators in between!", $this->tokens);
          return false;
        }
      }
      if($expressionOrOperator instanceof Operator && $expectExpression) {
        if($throwOnError) throw new ExpressionNotFoundException("Can't chain operators without expressions in between!", $this->tokens);
        return false;
      }
      $expectExpression = !$expectExpression;
    }
    // check if last one is no operator
    if($expectExpression) {
      if($throwOnError) throw new ExpressionNotFoundException("Cant end an expression with an operator", $this->tokens);
      return false;
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
  private function calculateRecursive(): Calculateable {
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
  public function calculate(): Calculateable {
    $expressionsAndOperatorsBackup = $this->getExpressionsAndOperatorsBackup();
    $calculateable = $this->calculateRecursive();
    $this->expressionsAndOperators = $expressionsAndOperatorsBackup;
    return $calculateable;
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\Nestable::getContent()
   */
  public function getContent(): array {
    $content = [];
    foreach ($this->expressionsAndOperators as $expressionOrOperator) {
      $content[] = $expressionOrOperator;
      if($expressionOrOperator instanceof Nestable) {
        $content = array_merge($content, $expressionOrOperator->getContent());
      }
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

