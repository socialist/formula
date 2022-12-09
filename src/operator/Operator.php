<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\Expression;
use InvalidArgumentException;

/**
 *
 * @author Timo Lehnertz
 *
 */
abstract class Operator {

  /**
   * Prioriry of this operator over other operators
   * @readonly
   */
  private int $priority;
  
  /**
   * Can left and right be intervhanged
   * @readonly
   */
  private bool $commutative;
  
  /**
   * Is lefthand expression needed
   * @readonly
   */
  private bool $needsLeft;
  
  /**
   * Is righthand expression needed
   * @readonly
   */
  private bool $needsRight;
  
  /**
   * Will use lefthand expression
   * @readonly
   */
  private bool $usesLeft;
  
  /**
   * Will use righthand expression
   * @readonly
   */
  private bool $usesRight;
  
  public function __construct(int $priority, bool $commutative, bool $needsLeft = true, bool $needsRight = true, bool $usesLeft = true, bool $usesRight = true) {
    $this->priority = $priority;
    $this->commutative = $commutative;
    $this->needsLeft = $needsLeft;
    $this->needsRight = $needsRight;
    $this->usesLeft = $usesLeft;
    $this->usesRight = $usesRight;
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
  public function needsLeft(): bool {
    return $this->needsLeft;
  }
  
  /**
   * @return bool needsLeft
   */
  public function needsRight(): bool {
    return $this->needsRight;
  }
  
  /**
   * @return bool usesLeft
   */
  public function usesLeft(): bool {
    return $this->usesLeft;
  }
  
  /**
   * @return bool usesRight
   */
  public function usesRight(): bool {
    return $this->usesRight;
  }
  
  /**
   * @param Calculateable $left
   * @param Expression $right
   * @throws InvalidArgumentException
   * @return \TimoLehnertz\formula\operator\Calculateable
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