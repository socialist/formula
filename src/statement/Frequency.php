<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

/**
 * @author Timo Lehnertz
 */
enum Frequency: int {

  case NEVER = 0;

  case SOMETIMES = 1;

  case ALWAYS = 2;

  public static function getHigher(Frequency $a, Frequency $b): Frequency {
    return $a > $b ? $a : $b;
  }

  public static function or(Frequency $a, Frequency $b): Frequency {
    if($a === $b) {
      return $a;
    } else {
      return Frequency::SOMETIMES;
    }
  }
}
