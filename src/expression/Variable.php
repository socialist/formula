<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\operator\Calculateable;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Variable implements Expression, Parseable {

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
    if($this->value === null) throw new ExpressionNotFoundException("Can't calculate. Variable $this->identifier has no value");
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
}