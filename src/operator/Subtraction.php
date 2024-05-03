<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use src\operator\OperatorType;
use src\type\Value;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class Subtraction extends Operator {

  private Expression $leftExpression;

  private Expression $rightExpression;

  public function __construct(Expression $leftExpression, Expression $rightExpression) {
    parent::__construct('-', 6, OperatorType::Infix);
    $this->leftExpression = $leftExpression;
    $this->rightExpression = $rightExpression;
  }

  public function run(): Value {}

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {}

  public function setScope(Scope $scope) {}

  public function getSubParts(): array {}

  public function validate(): Type {}
}