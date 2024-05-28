<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ExpressionStatement implements Statement {

  private Expression $expression;

  public function __construct(Expression $expression) {
    $this->expression = $expression;
  }

  public function validate(Scope $scope): StatementReturnType {
    return new StatementReturnType($this->expression->validate($scope), false, false);
  }

  public function run(Scope $scope): StatementReturn {
    return new StatementReturn($this->expression->run($scope), false, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }
}
