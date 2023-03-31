<?php
namespace TimoLehnertz\formula\expression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class TimeLiteral extends Number {

  private string $stringRepresentation;
  
  /**
   * @param int $value Timestamp
   */
  public function __construct(int $value, string $stringRepresentation) {
    parent::__construct($value);
    $this->stringRepresentation = $stringRepresentation;
  }
  
  /**
   * Will return a new time object when a matching format is found otherwise null
   * @return TimeLiteral|NULL
   */
  public static function fromString(string $string): ?TimeLiteral {
    if(strlen(trim($string)) === 0) return null;
    $dateObj = date_create_immutable($string);
    if($dateObj !== false) return new TimeLiteral($dateObj->getTimestamp(), $string);
    return null;
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\SubFormula::toString()
   */
  public function toString(): string {
    return '"'.$this->stringRepresentation.'"';
  }
}