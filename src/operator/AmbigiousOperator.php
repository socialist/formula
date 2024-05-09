<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * Represents an operator that got parsed but needs to be resolved based on context during validation stage
 * @author Timo Lehnertz
 */
class AmbigiousOperator extends Operator {

  private readonly int $tokenID;

  public function __construct(int $tokenID) {
    $this->tokenID = $tokenID;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    throw new \BadFunctionCallException('AmbigiousOperator has to be resolved first!');
  }

  public function getSubParts(): array {
    throw new \BadFunctionCallException('AmbigiousOperator has to be resolved first!');
  }

  public function validate(Scope $scope): Type {
    throw new \BadFunctionCallException('AmbigiousOperator has to be resolved first!');
  }
}
