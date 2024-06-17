<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ConstantExpression implements Expression {

  private readonly Type $type;

  private readonly Value $value;

  public function __construct(Type $type, Value $value) {
    $this->type = $type;
    $this->value = $value;
  }

  public function validate(Scope $scope): Type {
    return $this->type;
  }

  public function run(Scope $scope): Value {
    return $this->value->copy();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->value->toString();
  }

  public function buildNode(Scope $scope): Node {
    return new Node('ConstantExpression', [], ['type' => $this->type->buildNodeInterfaceType(),'value' => $this->value->toString()]);
  }
}