<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ArrayExpression implements Expression {

  private readonly array $expressions;

  private ArrayType $arrayType;

  public function __construct(array $expressions) {
    $this->expressions = $expressions;
  }

  public function validate(Scope $scope): Type {
    $types = [];
    foreach($this->expressions as $expression) {
      $types[] = $expression->validate($scope);
    }
    $elementType = CompoundType::buildFromTypes($types, true);
    $this->arrayType = new ArrayType(new IntegerType(true), $elementType, true);
    return $this->arrayType;
  }

  public function run(Scope $scope): Value {
    $values = [];
    foreach($this->expressions as $expression) {
      $values[] = $expression->run($scope);
    }
    return new ArrayValue($values, $this->arrayType);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $str = '';
    $del = '';
    foreach($this->expressions as $expression) {
      $str .= $del.$expression->toString($prettyPrintOptions);
      $del = ',';
    }
    return '{'.$str.'}';
  }

  public function buildNode(Scope $scope): Node {
    $inputs = [];
    foreach($this->expressions as $expression) {
      $inputs[] = $expression->buildNode($scope);
    }
    return new Node('ArrayExpression', $inputs);
  }
}
