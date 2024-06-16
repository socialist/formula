<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\ParsedOperator;

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
}
