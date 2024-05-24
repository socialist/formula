<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class TernaryExpression implements Expression {

  private readonly ?Expression $condition = null;

  private readonly ?Expression $leftExpression = null;

  private readonly ?Expression $rightExpression = null;

  public function __construct(?Expression $condition, ?Expression $leftExpression, ?Expression $rightExpression) {
    $this->condition = $condition;
    $this->leftExpression = $leftExpression;
    $this->rightExpression = $rightExpression;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->condition->toString($prettyPrintOptions).'?'.$this->leftExpression->toString($prettyPrintOptions).':'.$this->rightExpression->toString($prettyPrintOptions).'';
  }

  public function validate(Scope $scope): Type {
    $this->condition->validate($scope);
    $leftType = $this->leftExpression->validate($scope);
    $rightType = $this->rightExpression->validate($scope);
    return CompoundType::concatTypes($leftType, $rightType);
  }

  public function run(): Value {
    return $this->condition->run()->isTruthy() ? $this->leftExpression->run() : $this->rightExpression->run();
  }
}
