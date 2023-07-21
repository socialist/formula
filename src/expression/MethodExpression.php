<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\procedure\Scope;

/**
 *
 * @author Timo Lehnertz
 * 
 */
class MethodExpression implements Expression, Nestable, SubFormula {

  protected string $identifier;

  private array $parameterExpressions;

  private Scope $scope;
  
  public function __construct(string $identifier, array $parameterExpressions) {
    $this->identifier = $identifier;
    $this->parameterExpressions = $parameterExpressions;
  }
  
  /**
   * @inheritdoc
   */
  public function calculate(): Calculateable {
    $method = $this->scope->getMethod($this->identifier);
    if($method === null) throw new ExpressionNotFoundException("No method provided for $this->identifier!");
    $parameters = $this->getParameterValues();
    $value = call_user_func_array($method->getCallable(), $parameters);
    return MethodExpression::calculateableFromValue($value);
  }
  
  public static function calculateableFromValue($value) : Calculateable {
    if($value === null) return new NullExpression();
    if(is_numeric($value)) return new Number($value);
    if($value instanceof \DateTimeImmutable) {
      return new TimeLiteral($value);
    }
    if($value instanceof \DateTime) {
      return new TimeLiteral(new \DateTimeImmutable($value));
    }
    if($value instanceof \DateInterval) {
      return new TimeIntervalLiteral($value);
    }
    if(is_array($value)) {
    	return ArrayExpression::fromArray($value);
    }
    if(is_bool($value)) {
      return new BooleanExpression($value);
    }
    return StringLiteral::fromString($value);
  }
  
  /**
   * @psalm-mutation-free
   * @return string
   */
  public function getIdentifier(): string {
    return $this->identifier;
  }
  
  /**
   * @param string $identifier
   * @return string
   */
  public function setIdentifier(string $identifier): void {
    $this->identifier = $identifier;
  }
  
  /**
   * Will return the calculated values of all parameters
   * @return array<mixed>
   */
  private function getParameterValues(): array {
    $values = [];
    foreach($this->parameterExpressions as $parameter) {
      $values[] = $parameter->calculate()->getValue();
    }
    return $values;
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\Nestable::getContent()
   */
  public function getContent(): array {
    $content = [];
    foreach ($this->parameterExpressions as $parameter) {
      $content[] = $parameter;
      if($parameter instanceof Nestable) {
        $content = array_merge($content, $parameter->getContent());
      }
    }
    return $content;
  }
  
  public function validate(bool $throwOnError, Scope $scope): bool {
    $this->scope = $scope;
    foreach ($this->parameterExpressions as $parameter) {
      if($parameter instanceof Nestable) {
        if(!$parameter->validate($throwOnError, $scope)) return false;
      }
    }
    return true;
  }

  public function toString(): string {
    $parameters = '';
    $delimiter = '';
    foreach ($this->parameterExpressions as $parameter) {
      $parameters .= $delimiter.$parameter->toString();
      $delimiter = ',';
    }
    return "$this->identifier($parameters)";
  }
}