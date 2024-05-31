<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ArrayExpression implements Expression {

  private readonly ExpressionListExpression $expressionList;

  public function __construct(ExpressionListExpression $expressionList) {
    $this->expressionList = $expressionList;
  }

  public function run(Scope $scope): Value {
    return $this->expressionList->run($scope);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '('.$this->expressionList->toString().')';
  }

  public function validate(Scope $scope): Type {
    $this->expressionList->validate($scope);
  }

  public function buildNode(Scope $scope): array {
    return ['type' => 'Array','outerType' => $this->validate($scope)->buildNode($scope),'content' => $this->expressionList->buildNode($scope)];
  }
}
