<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\ParsedOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 *
 *         Represents an OperatorExpression whoose string representation differs from the default implementation
 */
class ComplexOperatorExpression extends OperatorExpression {

  private readonly ?Expression $outerLeftExpression;

  private readonly ParsedOperator $outerOperator;

  private readonly ?Expression $outerRightExpression;

  public function __construct(?Expression $innerLeftExpression, ImplementableOperator $innerOperator, ?Expression $innerRightExpression, ?Expression $outerLeftExpression, ParsedOperator $outerOperator, ?Expression $outerRightExpression) {
    parent::__construct($innerLeftExpression, $innerOperator, $innerRightExpression);
    $this->outerLeftExpression = $outerLeftExpression;
    $this->outerOperator = $outerOperator;
    $this->outerRightExpression = $outerRightExpression;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $str = '';
    if($this->outerLeftExpression !== null) {
      $str .= $this->outerLeftExpression->toString($prettyPrintOptions);
    }
    $str .= $this->outerOperator->toString($prettyPrintOptions);
    if($this->outerRightExpression !== null) {
      $str .= $this->outerRightExpression->toString($prettyPrintOptions);
    }
    return $str;
  }

  public function buildNode(Scope $scope): Node {
    $connected = [];
    if($this->outerLeftExpression !== null) {
      $connected[] = $this->outerLeftExpression->buildNode($scope);
    }
    if($this->outerRightExpression !== null) {
      $connected[] = $this->outerRightExpression->buildNode($scope);
    }
    return new Node('ComplexOperatorExpression', $connected, ['operator' => $this->outerOperator->toString(PrettyPrintOptions::buildDefault())]);
  }
}
