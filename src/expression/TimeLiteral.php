<?php
namespace TimoLehnertz\formula\expression;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class TimeLiteral extends Number {

  /**
   * @param int $value Timestamp
   */
  public function __construct(int $value) {
    parent::__construct($value);
  }
  
  /**
   * Will return a new time object when a matching format is found otherwise null
   * @return TimeLiteral|NULL
   */
  public static function fromString(string $string): ?TimeLiteral {
    $dateObj = date_create_immutable($string);
    if($dateObj !== false) return new TimeLiteral($dateObj->getTimestamp());
    return null;
  }
}

