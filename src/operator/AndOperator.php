<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\StatementValue;
use TimoLehnertz\formula\type\Type;
use src\operator\OperatorType;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class AndOperator extends Operator {

  private Expression $leftExpression;

  private Expression $rightExpression;

  public function __construct(Expression $leftExpression, Expression $rightExpression) {
    parent::__construct('&&', 14, OperatorType::Infix);
    $this->leftExpression = $leftExpression;
    $this->rightExpression = $rightExpression;
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->leftExpression->toString($prettyPrintOptions).'&&'.$this->rightExpression->toString($prettyPrintOptions);
  }

  public function run(): StatementValue {}

  public function setScope(Scope $scope) {}

  public function getSubParts(): array {}

  public function validate(): Type {}
}

