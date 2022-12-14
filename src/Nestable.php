<?php
namespace TimoLehnertz\formula;

/**
 *
 * @author Timo Lehnertz
 *        
 */
interface Nestable {
  
  /**
   * Returns an array containing all sub Operators and expressions
   * @return array<Operator|Expression>
   */
  public function getContent(): array;
  
  /**
   * Validates this and sub contents
   * @param bool $throwOnError
   * @return bool
   */
  public function validate(bool $throwOnError): bool;
}