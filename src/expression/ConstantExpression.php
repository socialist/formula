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
class ConstantExpression implements Expression {

  private readonly Value $value;

  public function __construct(Value $value) {
    $this->value = $value;
  }

  public function run(): Value {
    return $this->value->copy();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->value->toString();
  }

  public function getSubParts(): array {
    return [];
  }

  public function validate(Scope $scope): Type {
    return $this->value->getType();
  }
}