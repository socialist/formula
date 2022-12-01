<?php
namespace socialistFork\formula;

/**
 *
 * @author Timo Lehnertz
 *        
 */
interface Nestable {
  
  /**
   * Sets all sub variables
   * @param string $identifier
   * @param mixed $value
   */
  public function setVariable(string $identifier, $value): void;
  
  /**
   * Sets all sub methods
   * @param string $identifier
   * @param callable $method
   */
  public function setMethod(string $identifier, callable $method): void;
  
  /**
   * Validates this and sub nestables
   * @param bool $throwOnError
   */
  public function validate(bool $throwOnError): bool;
}

