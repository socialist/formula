<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression implements Expression {

  public readonly ?Expression $leftExpression;

  public readonly Operator $operator;

  public readonly ?Expression $rightExpression;

  public function __construct(?Expression $leftExpression, Operator $operator, ?Expression $rightExpression) {
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    $this->rightExpression = $rightExpression;
  }

  public function validate(Scope $scope): Type {
    $this->operator->validate($scope);
    return $this->operator->validateOperation($this->leftExpression?->validate($scope) ?? null, $this->rightExpression?->validate($scope) ?? null);
  }

  public function run(): Value {
    return $this->operator->operate($this->leftExpression?->run() ?? null, $this->rightExpression?->run() ?? null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    if($this->leftExpression !== null) {
      $string .= $this->leftExpression->toString($prettyPrintOptions);
    }
    $string .= $this->operator->toString($prettyPrintOptions);
    if($this->rightExpression !== null) {
      $string .= $this->rightExpression->toString($prettyPrintOptions);
    }
    return $string;
  }
}
