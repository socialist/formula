<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\operator\Calculateable;
use Exception;
use PhpParser\Node\Stmt\Foreach_;


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
   * @var callable
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
    if(is_array($value)) {
    	return Vector::fromArray($value);
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
      $param = new MathExpression();
      
      $param->parse($tokens, $index); // will throw on error
      if($param->size() == 0) throw new ExpressionNotFoundException("Invalid Method argument", $tokens, $index);
      
      $index--;
      $this->parameters []= $param;
      $first = false;
    }
    throw new ExpressionNotFoundException("Unexpected end of input", $tokens, $index);
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
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\Nestable::getContent()
   */
  public function getContent(): array {
    $content = [];
    foreach ($this->parameters as $parameter) {
      $content[] = $parameter;
      if($parameter instanceof Nestable) {
        $content = array_merge($content, $parameter->getContent());
      }
    }
    return $content;
  }
  
  /**
   * @param callable $method
   */
  public function setMethod(callable $method): void {
    $this->method = $method;
  }
  
  public function validate(bool $throwOnError): bool {
    foreach ($this->parameters as $parameter) {
      if($parameter instanceof Nestable) {
        if(!$parameter->validate($throwOnError)) return false;
      }
    }
    return true;
  }
}