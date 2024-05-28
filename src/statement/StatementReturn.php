<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\type\Value;

/**
 * Readonly class containing information about the termination of a statement
 *
 * @author Timo Lehnertz
 */
class StatementReturn {

  public readonly Value $returnValue;

  public readonly bool $returnFlag;

  public readonly bool $breakFlag;

  public readonly int $continueCount;

  public function __construct(Value $returnValue, bool $returnFlag, bool $breakFlag, int $continueCount) {
    $this->returnValue = $returnValue;
    $this->returnFlag = $returnFlag;
    $this->breakFlag = $breakFlag;
    $this->continueCount = $continueCount;
  }

  public function isTerminating(): bool {
    return $this->returnFlag || $this->breakFlag || $this->continueCount > 0;
  }
}
