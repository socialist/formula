<?php
namespace TimoLehnertz\formula;

interface SubFormula {
  
  /**
   * Returns the string representation of this formula
   * @return string
   */
  public function toString(): string;
}