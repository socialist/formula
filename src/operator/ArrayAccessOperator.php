<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class ArrayAccessOperator extends ImplementableOperator implements CoupledOperator {

  private Expression $indexExpression;

  private ?Type $indexType = null;

  private ?Scope $scope = null;

  public function __construct(Expression $indexExpression) {
    parent::__construct(ImplementableOperator::IMPLEMENTABLE_ARRAY_ACCESS);
    $this->indexExpression = $indexExpression;
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return '['.$this->indexExpression->toString($prettyPrintOptions).']';
  }

  public function validate(Scope $scope): void {
    $this->indexType = $this->indexExpression->validate($scope);
    $this->scope = $scope;
  }

  public function getCoupledExpression(): Expression {
    return $this->indexExpression;
  }
}
