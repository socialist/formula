<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\NoVariableValueException;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;

/**
 *
 * @author Timo Lehnertz
 *
 */
class VariableExpression implements Expression, SubFormula {

  private string $identifier;

  private Type $type;

  private Scope $scope;
  
  public function __construct(string $identifier) {
    $this->identifier = $identifier;
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