<?php
namespace socialistFork\formula\expression;

use DateInterval;
use DateTime;
use Exception;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class TimeIntervalLiteral extends Number {

  /**
   * @param int $value milliseconds
   */
  private function __construct(int $value) {
    parent::__construct($value);
  }
  
  /**
   * Will return a new time object when a matching format is found otherwise null
   * @return TimeLiteral|NULL
   */
  public static function fromString(string $string): ?TimeIntervalLiteral {
    try {
      $interval = new DateInterval($string);
      return new TimeIntervalLiteral(TimeIntervalLiteral::intervalToMillis($interval));
    } catch(Exception $e) {
      return nulL;
    }
  }
  
  public static function intervalToMillis(DateInterval $interval): int {
    $date = new DateTime("@0");
    $date->add($interval);
    return $date->getTimestamp();
  }
}

