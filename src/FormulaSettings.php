<?php
namespace TimoLehnertz\formula;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class FormulaSettings {

  public function __construct() {}

  public static function buildDefaultSettings(): FormulaSettings {
    return new FormulaSettings();
  }
}
