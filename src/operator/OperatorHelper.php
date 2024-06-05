<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

/**
 * @author Timo Lehnertz
 */
trait OperatorHelper {

  public readonly int $precedence;

  public readonly int $id;

  public function __construct(int $precedence, int $id) {
    $this->precedence = $precedence;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function getID(): int {
    return $this->id;
  }
}
