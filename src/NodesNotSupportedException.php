<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class NodesNotSupportedException extends \Exception {

  private readonly string $incompatiblePart;

  public function __construc(string $incompatiblePart, ?string $message = null) {
    parent::__construct($message);
    $this->incompatiblePart = $incompatiblePart;
  }
}
