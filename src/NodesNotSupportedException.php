<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class NodesNotSupportedException extends \Exception {

  //   private readonly string $incompatiblePart;
  public function __construct(string $incompatiblePart) {
    parent::__construct($incompatiblePart.' does not support nodes');
    //     $this->incompatiblePart = $incompatiblePart;
  }
}
