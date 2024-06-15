<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\expression\TypeExpression;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperator extends ParsedOperator {

  private readonly bool $explicit;

  private readonly Type $type;

  public function __construct(bool $explicit, Type $type) {
    parent::__construct();
    $this->explicit = $explicit;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->explicit) {
      return '('.$this->type->getIdentifier().')';
    } else {
      return '';
    }
  }

  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression {
    $typeCastOperator = new ImplementableOperator(ImplementableOperator::TYPE_TYPE_CAST);
    return new OperatorExpression($rightExpression, $typeCastOperator, new TypeExpression($this->type));
  }

  public function getOperatorType(): OperatorType {
    return OperatorType::InfixOperator;
  }

  public function getPrecedence(): int {
    return 3;
  }
}
