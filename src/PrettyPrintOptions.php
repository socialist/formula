<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class PrettyPrintOptions {

  public function __construct() {}

  public static function buildDefault(): PrettyPrintOptions {
    return new PrettyPrintOptions();
  }
}

