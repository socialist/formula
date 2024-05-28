<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\TypeValue;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class TypeExpression implements Expression {

  private readonly Type $initialType;

  private Type $validatedType;

  public function __construct(Type $type) {
    $this->initialType = $type;
  }

  public function validate(Scope $scope): Type {
    $this->validatedType = $this->initialType->validate($scope);
    return new TypeType($this->validatedType);
  }

  public function run(Scope $scope): Value {
    return new TypeValue($this->validatedType);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->validatedType->getIdentifier();
  }
}
