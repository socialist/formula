<?php
namespace TimoLehnertz\formula\expression;

use Exception;
use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\operator\Calculateable;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Variable implements Expression, Parseable, Nestable {

  private ?string $identifier = null;

  /**
   * @var float|string
   */
  private ?Calculateable $value = null;
  
  public function getIdentifier(): string {
    return $this->identifier;
  }

  /**
   *
   * @inheritdoc
   */
  public function calculate(): Calculateable {
    if($this->value === null) throw new ExpressionNotFoundException("Can't calculate. Variable $this->identifier has no value");
    return $this->value;
  }

  public function parse(array &$tokens, int &$index): bool {
    if($tokens[$index]->name != "I") return false;
    if(sizeof($tokens) <= $index + 1) { // parsing
      $this->identifier = $tokens[$index]->value;
      $index++;
      return true;
    }
    if($tokens[$index + 1]->name != "(") { // cant be "(" because then it would be a method
      $this->identifier = $tokens[$index]->value;
      $index++;
      return true;
    }
    return false;
  }
  
  public function setMethod(string $identifier, callable $method): void {
    // do nothing
  }
  
  public function setVariable(string $identifier, $value): void {
    if($this->identifier != $identifier) return;
    if($value === null) throw new Exception("Can't set value of variable $this->identifier to null!");
    $this->value = Method::calculateableFromValue($value);
  }

  public function validate(bool $throwOnError): bool {
    return true; // do nothing
  }
  
  public function getVariables(): array {
    return [$this];
  }
}