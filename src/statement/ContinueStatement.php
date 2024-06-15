<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\IntegerType;

/**
 * @author Timo Lehnertz
 */
class ContinueStatement implements Statement {

  private ?Expression $expression;

  public function __construct(?Expression $expression) {
    $this->expression = $expression;
  }

  public function validate(Scope $scope): StatementReturnType {
    if($this->expression !== null) {
      $expressionType = $this->expression->validate($scope);
      if(!($expressionType instanceof IntegerType)) {
        throw new FormulaValidationException('Continue expression can only be followed by integer expression');
      }
    }
    return new StatementReturnType(null, Frequency::ALWAYS, Frequency::NEVER);
  }

  public function run(Scope $scope): StatementReturn {
    return new StatementReturn(null, false, $this->expression?->run($scope)->toPHPValue() ?? 1);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->expression === null) {
      return 'continue;';
    }
    return 'continue '.$this->expression->toString($prettyPrintOptions).';';
  }

  public function getExpression(): ?Expression {
    return $this->expression;
  }
}
