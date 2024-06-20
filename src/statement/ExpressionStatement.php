<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class ExpressionStatement extends Statement {

  private Expression $expression;

  public function __construct(Expression $expression) {
    parent::__construct();
    $this->expression = $expression;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $this->expression->validate($scope);
    return new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
  }

  public function runStatement(Scope $scope): StatementReturn {
    $this->expression->run($scope);
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }
}
