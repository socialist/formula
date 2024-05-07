<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use src\operator\OperatorType;

class ArrayOperator extends Operator {

  private FormulaPart $indexExpression;

  public function __construct(FormulaPart $indexExpression) {
    parent::__construct('[]', 2, OperatorType::Postfix);
    $this->indexExpression = $indexExpression;
  }

  public function run(): Value {}

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {}

  public function setScope(Scope $scope) {
    $this->indexExpression->setScope($scope);
  }

  public function getSubParts(): array {
    return $this->indexExpression->getSubParts();
  }

  public function validate(): Type {
    $this->indexExpression->validate();
  }
}