<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\MemberAccsessType;
use TimoLehnertz\formula\type\MemberAccsessValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class MemberAccsessExpression extends IdentifierExpression {

  public function validate(Scope $scope): Type {
    return new MemberAccsessType($this->getIdentifier());
  }

  public function run(Scope $scope): Value {
    return new MemberAccsessValue($this->getIdentifier());
  }

  public function buildNode(Scope $scope): Node {
    return new Node('MemberAccsessExpression', [], ['identifier' => $this->identifier]);
  }
}
