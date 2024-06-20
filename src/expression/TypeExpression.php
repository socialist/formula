<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\TypeValue;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class TypeExpression implements Expression {

  private Type $type;

  public function __construct(Type $type) {
    $this->type = $type;
  }

  public function validate(Scope $scope): Type {
    return new TypeType($this->type, true);
  }

  public function run(Scope $scope): Value {
    return new TypeValue($this->type);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->type->getIdentifier();
  }

  public function buildNode(Scope $scope): Node {
    return new Node('TypeExpression', [], ['type' => $this->type->buildNodeInterfaceType()]);
  }
}
