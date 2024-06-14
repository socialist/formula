<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ArgumentListType;
use TimoLehnertz\formula\type\ArgumentListValue;
use TimoLehnertz\formula\type\FunctionArgument;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 */
class ArgumentListExpression implements Expression, CastableExpression {

  /**
   * @var array<Expression>
   */
  private readonly array $expressions;

  private ArgumentListType $type;

  public function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  public function getCastedExpression(Type $type, Scope $scope): ArgumentListExpression {
    if(!($type instanceof ArgumentListType)) {
      throw new \BadMethodCallException('ArgumentListExpression can only be casted to ArgumentList! Got '.$type::class);
    }
    $newExpressions = [];
    for($i = 0;$i < count($this->expressions);$i++) {
      $targetType = $type->getArgumentType($i);
      $actualType = $this->expressions[$i]->validate($scope);
      $newExpressions[] = OperatorExpression::castExpression($this->expressions[$i], $actualType, $targetType, $scope);
    }
    $castedExpression = new ArgumentListExpression($newExpressions);
    $castedType = $castedExpression->validate($scope);
    if(!$type->assignableBy($castedType)) {
      throw new FormulaValidationException('Could not cast function arguments from '.$this->type->getIdentifier().' to '.$type->getIdentifier());
    }
    return $castedExpression;
  }

  public function validate(Scope $scope): Type {
    $arguments = [];
    foreach($this->expressions as $expression) {
      $arguments[] = new FunctionArgument($expression->validate($scope), false);
    }
    $this->type = new ArgumentListType($arguments, false);
    return $this->type;
  }

  public function run(Scope $scope): Value {
    $values = [];
    foreach($this->expressions as $expression) {
      $values[] = $expression->run($scope);
    }
    return new ArgumentListValue($values, $this->type);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $delim = '';
    foreach($this->expressions as $expression) {
      $string .= $delim.$expression->toString($prettyPrintOptions);
      $delim = ',';
    }
    return $string;
  }

  public function buildNode(Scope $scope): array {
    $subNodes = [];
    foreach($this->expressions as $expression) {
      $subNodes[] = $expression->buildNode($scope);
    }
    return ['type' => 'Call','outerType' => $this->validate($scope)->buildNode(),'nodes' => $subNodes];
  }
}
