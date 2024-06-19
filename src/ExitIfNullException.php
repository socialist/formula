<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class ExitIfNullException extends \Exception {

  public function __construct() {
    parent::__construct();
  }
}
