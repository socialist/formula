<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\TypeExpression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperator extends ImplementableOperator implements CoupledOperator {

  private readonly bool $explicit;

  private readonly Type $type;

  public function __construct(bool $explicit, Type $type) {
    parent::__construct(ImplementableOperator::IMPLEMENTABLE_TYPE_CAST);
    $this->explicit = $explicit;
    $this->type = $type;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->explicit) {
      return '('.$this->type->getIdentifier().')';
    } else {
      return '';
    }
  }

  public function getCoupledExpression(): Expression {
    return new TypeExpression($this->type);
  }
}
