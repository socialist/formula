<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class ReturnStatement implements Statement {

  private ?Expression $expression;

  public function __construct(?Expression $expression) {
    $this->expression = $expression;
  }

  public function validate(Scope $scope): StatementReturnType {
    if($this->expression !== null) {
      return new StatementReturnType($this->expression->validate($scope), true, false, 0);
    }
    return new StatementReturnType(new VoidType(), true, true);
  }

  public function run(Scope $scope): StatementReturn {
    if($this->expression !== null) {
      return new StatementReturn($this->expression->run($scope), true, false, 0);
    } else {
      return new StatementReturn(new VoidValue(), true, false, 0);
    }
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->expression === null) {
      return 'return;';
    }
    return 'return '.$this->expression->toString($prettyPrintOptions).';';
  }
}
