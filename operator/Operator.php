<?php
namespace socialistFork\formula\operator;

use InvalidArgumentException;
use socialistFork\formula\expression\Expression;

/**
 *
 * @author Timo Lehnertz
 *
 */
abstract class Operator {

  /**
   * @readonly
   */
  private int $priority;
  
  /**
   * @readonly
   */
  private bool $commutative;
  
  /**
   * @readonly
   */
  private bool $needsLeft;
  
  public function __construct(int $priority, bool $commutative, bool $needsLeft = true) {
    $this->priority = $priority;
    $this->commutative = $commutative;
    $this->needsLeft = $needsLeft;
  }
  
  /**
   * @return int priority
   */
  public function getPriority(): int {
    return $this->priority;
  }
  
  /**
   * @return bool needsLeft
   */
  public function doesNeedsLeft(): bool {
    return $this->needsLeft;
  }
  
  /**
   * @param Calculateable $left
   * @param Expression $right
   * @throws InvalidArgumentException
   * @return \socialistFork\formula\operator\Calculateable
   */
  public function calculate(Calculateable $left, Expression $right) {
    try {
      return $this->doCalculate($left->calculate($this), $right->calculate());
    } catch(InvalidArgumentException $e) {
      if($this->commutative) { // try other direction
        return $this->doCalculate($right->calculate($this), $left->calculate());
      } else {
        throw $e;
      }
    }
  }
  
  /**
   * @param string $name
   * @return Operator
   */
  public static function fromString(string $name): Operator {
    switch($name) {
      case "+":   return new Increment();
      case "-":   return new Subtraction();
      case "*":   return new Multiplication();
      case "/":   return new Division();
      case "^":   return new XorOperator();
      case "&&":  return new AndOperator();
      case "||":  return new OrOperator();
      case "!=":  return new NotEqualsOperator();
      case "!":   return new NotOperator();
      case "==":  return new EqualsOperator();
      case "<":  return new SmallerOperator();
      case ">":  return new GreaterOperator();
      case "<=":  return new SmallerEqualsOperator();
      case "<":   return new SmallerOperator();
      case ">=":  return new GreaterEqualsOperator();
      case "<":   return new GreaterOperator();
      default: throw new \Exception("Invalid operator: $name"); // shouldnt happen as this gets sorted out in tokenizer stage
    }
  }

  /**
   * @param mixed $left
   * @param mixed $right
   * @return mixed
   */
  public abstract function doCalculate(Calculateable $left, Calculateable $right): Calculateable;
}