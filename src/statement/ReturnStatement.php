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
class ReturnStatement extends Statement {

  private ?Expression $expression;

  public function __construct(?Expression $expression) {
    parent::__construct();
    $this->expression = $expression;
  }

  public function validate(Scope $scope): StatementReturnType {
    return new StatementReturnType($this->expression?->validate($scope) ?? new VoidType(), Frequency::ALWAYS, Frequency::ALWAYS);
  }

  public function run(Scope $scope): StatementReturn {
    return new StatementReturn($this->expression?->run($scope) ?? new VoidValue(), false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->expression === null) {
      return 'return;';
    }
    return 'return '.$this->expression->toString($prettyPrintOptions).';';
  }

  public function getExpression(): ?Expression {
    return $this->expression;
  }
}
