<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ExpressionStatement extends Statement {

  private Expression $expression;

  public function __construct(Expression $expression) {
    parent::__construct();
    $this->expression = $expression;
  }

  public function validate(Scope $scope): StatementReturnType {
    $this->expression->validate($scope);
    return new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
  }

  public function run(Scope $scope): StatementReturn {
    $this->expression->run($scope);
    return new StatementReturn(null, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }
}
