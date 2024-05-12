<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperator extends Operator {

  private readonly bool $explicit;

  private Type $type;

  public function __construct(bool $explicit, Type $type) {
    parent::__construct(Operator::TYPE_TYPE_CAST, OperatorType::Prefix, 3, false);
    $this->explicit = $explicit;
    $this->type = $type;
  }

  public function defineReferences(): void {
    // nothing to define
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->explicit) {
      return '('.$this->type->getIdentifier().')';
    } else {
      return '';
    }
  }

  public function getSubParts(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    $this->type = $this->type->validate($scope);
    return $this->type;
  }

  public function operate(?Expression $leftExpression, ?Expression $rightExpression): Value {
    if($rightExpression === null) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    return $rightExpression->run()->operate($this, null);
  }
}
