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

  public readonly ?Value $returnValue;

  public readonly bool $breakFlag;

  public readonly bool $continueFlag;

  public function __construct(?Value $returnValue, bool $breakFlag, bool $continueFlag) {
    $this->returnValue = $returnValue;
    $this->breakFlag = $breakFlag;
    $this->continueFlag = $continueFlag;
  }

  public function isTerminating(): bool {
    return $this->returnValue !== null || $this->breakFlag || $this->continueFlag;
  }
}
