<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class BreakStatement extends Statement {

  public function __construct() {
    parent::__construct();
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    return new StatementReturnType(null, Frequency::ALWAYS, Frequency::NEVER);
  }

  public function runStatement(Scope $scope): StatementReturn {
    return new StatementReturn(null, true, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'break;';
  }
}
