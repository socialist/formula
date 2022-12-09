<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\expression\Expression;

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
  public function add(Calculateable $summand): Calculateable;
  
  /**
   * @param Calculateable $difference
   * @throws \InvalidArgumentException
   */
  public function subtract(Calculateable $difference): Calculateable;
  
  /**
   * @param Calculateable $factor
   * @throws \InvalidArgumentException
   */
  public function multiply(Calculateable $factor): Calculateable;
  
  /**
   * @param Calculateable $divisor
   * @throws \InvalidArgumentException
   */
  public function divide(Calculateable $divisor): Calculateable;
  
  /**
   * @param Calculateable $power
   * @throws \InvalidArgumentException
   */
  public function pow(Calculateable $power): Calculateable;
  
  public function getValue();
}