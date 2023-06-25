<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\NoVariableValueException;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Variable implements Expression, Parseable, SubFormula {

  /**
   * @var ?string
   */
  private ?string $identifier = null;

  /**
   * @var float|string
   */
  private ?Calculateable $value = null;

  /**
   *
   * @inheritdoc
   */
  public function calculate(): Calculateable {
    if($this->value === null) throw new NoVariableValueException("Can't calculate. Variable $this->identifier has no value", $this->identifier);
    return $this->value->calculate();
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
  
  /**
   * @param Calculateable $value
   */
  public function setValue(Calculateable $value): void {
    $this->value = $value;
  }
  
  /**
   * @return string
   */
  public function getIdentifier(): string {
    return $this->identifier;
  }
  
  /**
   * @param string $identifier
   */
  public function setIdentifier(string $identifier): void {
    $this->identifier = $identifier;
  }

  /**
   * Unsets this variables value and will throw an exception if used in calculation
   */
  public function reset(): void {
    $this->value = null;
  }
  
  /**
   * @return string
   */
  public function toString(): string {
    return $this->identifier;
  }
}