<?php
namespace TimoLehnertz\formula\expression;

use DateInterval;
use DateTime;
use Exception;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class TimeIntervalLiteral extends Number {

  private string $stringRepresentation;
  
  /**
   * @param int $value milliseconds
   */
  private function __construct(int $value, string $stringRepresentation) {
    parent::__construct($value);
    $this->stringRepresentation = $stringRepresentation;
  }
  
  /**
   * Will return a new time object when a matching format is found otherwise null
   * @return TimeLiteral|NULL
   */
  public static function fromString(string $string): ?TimeIntervalLiteral {
    if(strlen(trim($string)) === 0) return null;
    if(!preg_match('/^P(?:\d+Y)?(?:\d+M)?(?:\d+W)?(?:\d+D)?(?:T(?:\d+H)?(?:\d+M)?(?:\d+S)?)?$/', $string)) {
      return null;
    }
    try {
      $interval = new DateInterval($string);
      return new TimeIntervalLiteral(TimeIntervalLiteral::intervalToMillis($interval), $string);
    } catch(Exception $e) {
      return nulL;
    }
  }
  
  public static function intervalToMillis(DateInterval $interval): int {
    $date = new DateTime("@0");
    $date->add($interval);
    return $date->getTimestamp();
  }
  
  /**
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\SubFormula::toString()
   */
  public function toString(): string {
    return "'".$this->stringRepresentation."'";
  }
}