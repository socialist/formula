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
class BracketExpression implements Expression {

  public readonly Expression $expression;

  public function __construct(Expression $expression) {
    $this->expression = $expression;
  }

  public function run(): Value {
    return $this->expression->run();
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '('.$this->expression->toString($prettyPrintOptions).')';
  }

  public function getSubParts(): array {
    return $this->expression->getSubParts();
  }

  public function validate(Scope $scope): Type {
    return $this->expression->validate($scope);
  }
}
