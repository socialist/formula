<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 */
class ReturnStatement extends Statement {

  private ?Expression $expression;

  public function __construct(?Expression $expression) {
    parent::__construct();
    $this->expression = $expression;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    if($this->expression === null) {
      if($allowedReturnType !== null && !$allowedReturnType->assignableBy(new VoidType())) {
        throw new FormulaValidationException('Expected '.$allowedReturnType->getIdentifier().'. Can\'t return void here');
      }
      return new StatementReturnType(new VoidType(), Frequency::ALWAYS, Frequency::ALWAYS);
    } else if($allowedReturnType !== null) {
      $implicidType = $this->expression->validate($scope);
      $this->expression = OperatorExpression::castExpression($this->expression, $implicidType, $allowedReturnType, $scope, $this);
    }
    return new StatementReturnType($this->expression->validate($scope), Frequency::ALWAYS, Frequency::ALWAYS);
  }

  public function runStatement(Scope $scope): StatementReturn {
    return new StatementReturn($this->expression?->run($scope) ?? new VoidValue(), false, false);
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
