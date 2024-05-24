<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

/**
 * @author Timo Lehnertz
 */
trait OperatorHelper {

  public readonly int $precedence;

  public function __construct(int $precedence) {
    $this->precedence = $precedence;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }
}
