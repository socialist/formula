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

/**
 * @author Timo Lehnertz
 */
class CallExpression implements Expression {

  /**
   * @var array<Expression>
   */
  private readonly array $expressions;

  private ArgumentListType $type;

  public function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  public function validate(Scope $scope): Type {
    $arguments = [];
    foreach($this->expressions as $expression) {
      $arguments[] = new FunctionArgument($expression->validate($scope), false);
    }
    $this->type = new ArgumentListType($arguments);
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
    return ['type' => 'ExpressionList','outerType' => $this->validate($scope)->buildNode(),'nodes' => $subNodes];
  }
}
