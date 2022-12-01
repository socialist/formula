<?php
namespace socialistFork\formula\operator;

use socialistFork\formula\expression\Expression;

/**
 *
 * @author Timo Lehnertz
 * 
 */
interface Calculateable extends Expression {
  
  public function isTruthy(): bool;
  
  /**
   * @param Calculateable $summand
   * @throws \InvalidArgumentException
   */
  public function add(Calculateable $summand);
  
  /**
   * @param Calculateable $difference
   * @throws \InvalidArgumentException
   */
  public function subtract(Calculateable $difference);
  
  /**
   * @param Calculateable $factor
   * @throws \InvalidArgumentException
   */
  public function multiply(Calculateable $factor);
  
  /**
   * @param Calculateable $divisor
   * @throws \InvalidArgumentException
   */
  public function divide(Calculateable $divisor);
  
  /**
   * @param Calculateable $power
   * @throws \InvalidArgumentException
   */
  public function pow(Calculateable $power);
  
  public function getValue();
}