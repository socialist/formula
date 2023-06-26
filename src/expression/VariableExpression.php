<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\NoVariableValueException;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;
use TimoLehnertz\formula\procedure\Method;

/**
 *
 * @author Timo Lehnertz
 *
 */
class VariableExpression implements Expression, Parseable, SubFormula {

  /**
   * @var ?string
   */
  private ?string $identifier;

  /**
   * @var float|string
   */
  private ?Calculateable $value = null;

  private Type $type;

  private Scope $scope;
  
  public function __construct(?string $identifier = null, ?Type $type = null) {
    $this->identifier = $identifier;
    $this->type = $type;
  }
  
  /**
   *
   * @inheritdoc
   */
  public function calculate(): Calculateable {
    $variable = $this->scope->getvariable($this->identifier);
    if($variable === null) throw new NoVariableValueException("Can't calculate. Variable $this->identifier is undefined", $this->identifier);
    return MethodExpression::calculateableFromValue($variable->getValue());
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