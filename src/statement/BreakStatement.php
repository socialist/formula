<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class BreakStatement implements Statement {

  public function validate(Scope $scope): StatementReturnType {
    return new StatementReturnType(new VoidType(), false, false);
  }

  public function run(Scope $scope): StatementReturn {
    return new StatementReturn(new VoidValue(), false, true, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'break;';
  }
}
