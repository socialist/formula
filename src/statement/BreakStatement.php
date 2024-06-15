<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class BreakStatement implements Statement {

  public function validate(Scope $scope): StatementReturnType {
    return new StatementReturnType(null, Frequency::ALWAYS, Frequency::NEVER);
  }

  public function run(Scope $scope): StatementReturn {
    return new StatementReturn(null, true, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'break;';
  }
}
