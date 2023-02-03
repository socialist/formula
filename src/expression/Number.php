<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class Number implements Calculateable, SubFormula {
  
  /**
   * @var float
   */
  private float $value;
  
  /**
   * @readonly
   * @var bool
   */
  private bool $placeholder;
  
  public function __construct(string $value, bool $placeholder = false) {
    if($value == 'NAN') {
      $this->value = NAN;
    } else {
      $this->value = floatval($value);
    }
    $this->placeholder = $placeholder;
  }
  
  /**
   * 
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\expression\Expression::calculate()
   */
  public function calculate(): Calculateable {
    return $this;
  }
  
  public function getValue() {
    return $this->value;
  }
  
  public function add(Calculateable $summand): Calculateable {
    if(!$summand instanceof Number) throw new \InvalidArgumentException("Can only add numbers got ". get_class($summand));
    return new Number($this->value + $summand->getValue());
  }
  
  public function subtract(Calculateable $difference): Calculateable {
    if(!$difference instanceof Number) throw new \InvalidArgumentException("Can only subtract numbers got ". get_class($difference));
    return new Number($this->value - $difference->getValue());
  }
  
  public function pow(Calculateable $power): Calculateable {
    if(!$power instanceof Number) throw new \InvalidArgumentException("Can only power numbers got ". get_class($power));
    return new Number(pow($this->value, $power->getValue()));
  }
  
  public function divide(Calculateable $divisor): Calculateable {
    if(!$divisor instanceof Number) throw new \InvalidArgumentException("Can only divide numbers got ". get_class($divisor));
    $divisorValue = $divisor->getValue();
    if($divisorValue == 0 || is_nan($divisorValue)) return new Number(NAN);
    return new Number($this->value / $divisorValue);
  }
  
  public function multiply(Calculateable $factor): Calculateable {
    if(!$factor instanceof Number) throw new \InvalidArgumentException("Can only multiply numbers got ". get_class($factor));
    return new Number($this->value * $factor->getValue());
  }
  
  public function isTruthy(): bool {
    return $this->value != 0;
  }

  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\SubFormula::toString()
   */
  public function toString(): string {
    if($this->isPlaceholder()) return '';
    return strval($this->value);
  }
  
  public function isPlaceholder(): bool {
    return $this->placeholder;
  }
}