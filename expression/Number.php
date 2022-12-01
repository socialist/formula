<?php
namespace socialistFork\formula\expression;

use socialistFork\formula\operator\Calculateable;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class Number implements Calculateable {
  
  /**
   * @var float
   */
  private float $value;
  
  public function __construct(string $value) {
    $this->value = floatval($value);
  }
  
  /**
   * 
   * {@inheritDoc}
   * @see \socialistFork\formula\expression\Expression::calculate()
   */
  public function calculate(): Calculateable {
    return $this;
  }
  
  public function getValue() {
    return $this->value;
  }
  
  public function add(Calculateable $summand) {
    if(!$summand instanceof Number) throw new \InvalidArgumentException("Can only add numbers got ". get_class($summand));
    return new Number($this->value + $summand->getValue());
  }
  
  public function subtract(Calculateable $difference) {
    if(!$difference instanceof Number) throw new \InvalidArgumentException("Can only subtract numbers got ". get_class($difference));
    return new Number($this->value - $difference->getValue());
  }
  
  public function pow(Calculateable $power) {
    if(!$power instanceof Number) throw new \InvalidArgumentException("Can only power numbers got ". get_class($power));
    return new Number(pow($this->value, $power->getValue()));
  }
  
  public function divide(Calculateable $divisor) {
    if(!$divisor instanceof Number) throw new \InvalidArgumentException("Can only divide numbers got ". get_class($divisor));
    $divisorValue = $divisor->getValue();
    if($divisorValue == 0 || is_nan($divisorValue)) return new Number(NAN);
    return new Number($divisorValue == 0 ? NAN : $this->value / $divisorValue);
  }
  
  public function multiply(Calculateable $factor) {
    if(!$factor instanceof Number) throw new \InvalidArgumentException("Can only multiply numbers got ". get_class($factor));
    return new Number($this->value * $factor->getValue());
  }
  
  public function isTruthy(): bool {
    return $this->value != 0;
  }

}