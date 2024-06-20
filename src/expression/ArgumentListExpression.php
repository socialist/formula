<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListValue;

/**
 * @author Timo Lehnertz
 */
class ArgumentListExpression implements Expression, CastableExpression {

  /**
   * @var array<Expression>
   */
  private readonly array $expressions;

  public function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  public function getCastedExpression(Type $type, Scope $scope): ArgumentListExpression {
    if(!($type instanceof OuterFunctionArgumentListType)) {
      throw new FormulaBugException('ArgumentListExpression can only be casted to OuterFunctionArgumentListType! Got '.$type::class);
    }
    $newExpressions = [];
    for($i = 0;$i < count($this->expressions);$i++) {
      $targetType = $type->getArgumentType($i);
      $actualType = $this->expressions[$i]->validate($scope);
      $expression = OperatorExpression::castExpression($this->expressions[$i], $actualType, $targetType, $scope, $this->expressions[$i]);
      $newExpressions[] = $expression;
    }
    $castedExpression = new ArgumentListExpression($newExpressions);
    $castedExpression->validate($scope);
    return $castedExpression;
  }

  public function validate(Scope $scope): Type {
    $arguments = [];
    foreach($this->expressions as $expression) {
      $arguments[] = new OuterFunctionArgument($expression->validate($scope), false, false);
    }
    return new OuterFunctionArgumentListType($arguments, false);
  }

  public function run(Scope $scope): Value {
    $values = [];
    foreach($this->expressions as $expression) {
      $values[] = $expression->run($scope);
    }
    return new OuterFunctionArgumentListValue($values);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $delim = '';
    foreach($this->expressions as $expression) {
      $string .= $delim.$expression->toString($prettyPrintOptions);
      $delim = ',';
    }
    return '('.$string.')';
  }

  public function buildNode(Scope $scope): Node {
    $inputs = [];
    foreach($this->expressions as $expression) {
      $inputs[] = $expression->buildNode($scope);
    }
    return new Node('ArgumentListExpression', $inputs);
  }

  public function getExpressions(): array {
    return $this->expressions;
  }
}
