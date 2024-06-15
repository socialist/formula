<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

/**
 * @author Timo Lehnertz
 */
class ParsingSkippedException extends \Exception {

  public function __construct(string $message = '') {
    parent::__construct($message);
  }
}
