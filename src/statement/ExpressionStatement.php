<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ExpressionStatement implements Statement {

  private Expression $expression;

  public function __construct(Expression $expression) {
    $this->expression = $expression;
  }

  public function defineReferences(): void {
    // expressions dont define anything
  }

  public function run(): StatementValue {
    $value = $this->expression->run();
    return new StatementValue($value);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->expression->toString($prettyPrintOptions).';';
  }

  public function setScope(Scope $scope) {
    $this->expression->setScope($scope);
  }

  public function getSubParts(): array {
    return $this->expression->getSubParts();
  }

  public function validate(): Type {
    return $this->expression->validate();
  }
}
