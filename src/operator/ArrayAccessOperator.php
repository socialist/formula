<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ArrayAccessOperator extends Operator {

  private readonly FormulaPart $indexExpression;

  private ?Type $indexType = null;

  public function __construct(FormulaPart $indexExpression) {
    parent::__construct(Operator::TYPE_ARRAY_ACCESS, '[]', 2, OperatorType::Postfix);
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

  public function validate(Scope $scope): Type {
    $this->indexType = $this->indexExpression->validate($scope);
    return $this->indexType;
  }

  public function getIndexType(): Type {
    if($this->indexType === null) {
      throw new \BadFunctionCallException('Validate first');
    }
    return $this->indexType;
  }
}
