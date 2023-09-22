<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use InvalidArgumentException;

/**
 *
 * @author Timo Lehnertz
 *
 */
abstract class Operator extends FormulaPart {

  /**
   * precedence of this operator over other operators, lower is higher priority
   * source https://en.cppreference.com/w/cpp/language/operator_precedence
   * @readonly
   */
  private int $precedence;
  
  /**
   * Can left and right be interchanged
   * @readonly
   */
  private bool $commutative;
  
  /**
   * Is lefthand expression required
   * @readonly
   */
  private bool $needsLeft;
  
  /**
   * Is righthand expression required
   * @readonly
   */
  private bool $needsRight;
  
  /**
   * Will use lefthand expression if available
   * @readonly
   */
  private bool $usesLeft;
  
  /**
   * Will use righthand expression if available
   * @readonly
   */
  private bool $usesRight;
  
  private ?string $stringRepresentation;
  
  public function __construct(?string $stringRepresentation, int $precedence, bool $commutative, bool $needsLeft = true, bool $needsRight = true, bool $usesLeft = true, bool $usesRight = true) {
    $this->precedence = $precedence;
    $this->commutative = $commutative;
    $this->needsLeft = $needsLeft;
    $this->needsRight = $needsRight;
    $this->usesLeft = $usesLeft;
    $this->usesRight = $usesRight;
    $this->stringRepresentation = $stringRepresentation;
  }
  
  /**
   * MUST validate this operator and all sub parts based on the input expressions
   * MUST check for type compatibility
   * CAN validate left and right expression but doesnt have to
   */
  public abstract function validate(Scope $scope, ?Expression $leftExpression, ?Expression $rightExpression, FormulaSettings $formulaSettings): Type;
  
  /**
   * @throws InvalidArgumentException
   */
  public function calculate(Expression $left, Expression $right): Calculateable {
    try {
      return $this->doCalculate($left->calculate(), $right->calculate());
    } catch(InvalidArgumentException $e) {
      if($this->commutative) { // try other direction
        return $this->doCalculate($right->calculate(), $left->calculate());
      } else {
        throw $e;
      }
    }
  }
  
  public abstract function doCalculate(?ReturnValue $left, ?ReturnValue $right): ReturnValue;
  
  /**
   * @deprecated
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

  public function getSubParts(): array {
    return [];
  }
  
  public function getPrecedence(): int {
    return $this->precedence;
  }
  
  public function needsLeft(): bool {
    return $this->needsLeft;
  }
  
  public function needsRight(): bool {
    return $this->needsRight;
  }
  
  public function usesLeft(): bool {
    return $this->usesLeft;
  }
  
  public function usesRight(): bool {
    return $this->usesRight;
  }
  
  /**
   * Default implementation might be overridden
   */
  public function toString(PrettyPrintOptions $prettyprintOptions): string {
    return $this->stringRepresentation;
  }
}