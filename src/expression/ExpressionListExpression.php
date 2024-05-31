<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ExpressionListType;
use TimoLehnertz\formula\type\ExpressionListValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ExpressionListExpression implements Expression {

  /**
   * @var array<Expression>
   */
  private readonly array $expressions;

  private Type $type;

  public function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  public function validate(Scope $scope): Type {
    $types = [];
    foreach($this->expressions as $expression) {
      $types[] = $expression->validate($scope);
    }
    $this->type = new ExpressionListType($types);
    return $this->type;
  }

  public function run(Scope $scope): Value {
    $values = [];
    foreach($this->expressions as $expression) {
      $values[] = $expression->run($scope);
    }
    return new ExpressionListValue($values, $this->type);
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
