<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\type\Type;

/**
 * Readonly class containing information about the termination of a statement
 *
 * @author Timo Lehnertz
 */
class StatementReturnType {

  public readonly ?Type $returnType;

  public readonly bool $mayReturn;

  public readonly bool $alwaysReturns;

  public function __construct(?Type $returnType, bool $mayReturn, bool $alwaysReturns) {
    $this->returnType = $returnType;
    $this->mayReturn = $mayReturn;
    $this->alwaysReturns = $alwaysReturns;
    if($returnType === null && ($mayReturn || $alwaysReturns)) {
      throw new \UnexpectedValueException('Return type cant be null');
    }
  }
}
