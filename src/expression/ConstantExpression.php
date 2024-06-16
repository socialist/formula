<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ConstantExpression extends Expression {

  private readonly Type $type;

  private readonly Value $value;

  public function __construct(Type $type, Value $value) {
    parent::__construct();
    $this->type = $type;
    $this->value = $value;
  }

  public function validateStatement(Scope $scope): Type {
    return $this->type;
  }

  public function run(Scope $scope): Value {
    return $this->value->copy();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->value->toString($prettyPrintOptions);
  }

  public function buildNode(Scope $scope): array {
    return ['type' => 'Constant','outerType' => $this->validate($scope)->buildNode(),'value' => $this->value->buildNode()];
  }
}