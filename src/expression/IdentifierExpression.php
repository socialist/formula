<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class IdentifierExpression implements Expression {

  private readonly string $identifier;

  public function __construct(string $identifier) {
    $this->identifier = $identifier;
  }

  public function validate(Scope $scope): Type {
    if(!$scope->isDefined($this->identifier)) {
      throw new FormulaValidationException($this->identifier.' is not defined');
    }
    return $scope->getType($this->identifier);
  }

  public function run(Scope $scope): Value {
    return $scope->get($this->identifier);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->identifier;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }
}
