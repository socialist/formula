<?php
namespace socialistFork\formula\expression;

use Exception;
use socialistFork\formula\Nestable;
use socialistFork\formula\Parseable;
use socialistFork\formula\ParsingException;
use socialistFork\formula\operator\Calculateable;
use socialistFork\formula\ExpressionNotFoundException;

/**
 *
 * @author Timo Lehnertz
 * 
 */
class Method implements Expression, Parseable, Nestable {

  /**
   * Identifier of this method. To be set by parse()
   * @var string|null
   */
  protected ?string $identifier = null;

  /**
   * Parameters of this method. To be set by parse()
   * @var array<MathExpression>|null
   */
  private ?array $parameters = null;

  /**
   * Callable method
   */
  private $method = null;
  
  /**
   * @inheritdoc
   */
  public function calculate(): Calculateable {
    if($this->method == null) throw new ExpressionNotFoundException("No method provided for $this->identifier!");
    $parameters = $this->getParameterValues();
    $value = call_user_func_array($this->method, $parameters);
    if($value === null) throw new Exception("Return value of function $this->identifier was null");
    return Method::calculateableFromValue($value);
  }
  
  public static function calculateableFromValue($value) : Calculateable {
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
    return StringLiteral::fromString($value);
  }

  public function parse(array &$tokens, int &$index): bool {
    // identifier
    if($tokens[$index]->name != "I") return false;
    if(sizeof($tokens) <= $index + 3) return false; // must be variable as there are no parameters following
    if($tokens[$index + 1]->name != "(") return false; // must be variable    $this->identifier = $tokens[$index]['value'];
    $this->identifier = $tokens[$index]->value;
    // parse parameters
    $this->parameters = [];
    $index += 2; // skipping identifier and opening bracket
    $first = true;
    for ($index; $index < sizeof($tokens); $index++) {
      $token = $tokens[$index];
      if($token->name == ')') {
        $index++;
        return true; // parsing succsessfull
      }
      if($first && $token->name == ',') throw new ParsingException("", $token);
      if(!$first && $token->name != ',') throw new ParsingException("", $token);
      if(!$first) $index++;
      $param = new MathExpression(null);
      
      $param->parse($tokens, $index); // will throw on error
      
      $index--;
      $this->parameters []= $param;
      $first = false;
    }
    throw new ExpressionNotFoundException("Unexpected end of input");
  }
  
  /**
   * @param string $identifier
   * @param mixed $value
   */
  public function setVariable(string $identifier, $value): void {
    foreach ($this->parameters as $parameter) {
      $parameter->setVariable($identifier, $value);
    }
  }
  
  /**
   * @param string $identifier
   */
  public function setMethod(string $identifier, callable $method): void {
    foreach ($this->parameters as $parameter) {
      $parameter->setMethod($identifier, $method);
    }
    if($this->identifier == $identifier) {
      $this->method = $method;
    }
  }
  
  /**
   * @psalm-mutation-free
   * @return string
   */
  public function getIdentifier(): string {
    return $this->identifier;
  }
  
  /**
   * Will return the calculated values of all parameters
   * @return array<mixed>
   */
  private function getParameterValues(): array {
    $values = [];
    foreach($this->parameters as $parameter) {
      $values[] = $parameter->calculate()->getValue();
    }
    return $values;
  }
  
  public function validate(bool $throwOnError): bool {
    foreach($this->parameters as $parameter) {
      if(!$parameter->validate($throwOnError)) {
        return false;
      }
    }
    return true;
  }
}