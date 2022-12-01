<?php
namespace TimoLehnertz\formula\expression;

/**
 *
 * @author Timo Lehnertz
 *
 */
class Percent extends Number {

  /**
   * Percent constructor
   * @param string $value
   */
  public function __construct(string $value) {
    parent::__construct(floatval(str_replace('%', '', $value)) / 100);
  }
}