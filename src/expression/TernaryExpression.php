<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class TernaryExpression implements Expression {

  private readonly Expression $condition;

  private readonly Expression $leftExpression;

  private readonly Expression $rightExpression;

  public function __construct(Expression $condition, Expression $leftExpression, Expression $rightExpression) {
    $this->condition = $condition;
    $this->leftExpression = $leftExpression;
    $this->rightExpression = $rightExpression;
  }

  public function validate(Scope $scope): Type {
    $this->condition->validate($scope);
    $leftType = $this->leftExpression->validate($scope);
    $rightType = $this->rightExpression->validate($scope);
    return CompoundType::buildFromTypes([$leftType,$rightType], true);
  }

  public function run(Scope $scope): Value {
    return $this->condition->run($scope)->isTruthy() ? $this->leftExpression->run($scope) : $this->rightExpression->run($scope);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ''.$this->condition->toString($prettyPrintOptions).'?'.$this->leftExpression->toString($prettyPrintOptions).':'.$this->rightExpression->toString($prettyPrintOptions).'';
  }

  public function buildNode(Scope $scope): Node {
    return new Node('TernaryExpression', [$this->condition->buildNode($scope),$this->leftExpression->buildNode($scope),$this->rightExpression->buildNode($scope)]);
  }
}
