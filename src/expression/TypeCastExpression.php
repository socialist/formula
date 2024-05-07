<?php
namespace src\expression;

use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\Type;

class TypeCastExpression implements Expression {

  private readonly bool $explicit;

  private Type $type;

  private readonly Expression $expression;

  private Scope $scope;

  public function __construct(bool $explicit, Type $type, Expression $expression) {
    $this->explicit = $explicit;
    $this->type = $type;
    $this->expression = $expression;
  }

  public function defineReferences(): void {
    // nothing to define
  }

  public function run(): Value {
    $value = $this->expression->run();
    return $value->getType()
      ->castTo($this->type, $value);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->explicit) {
      return '('.$this->type->getIdentifier().') '.$this->expression->toString($prettyPrintOptions);
    } else {
      return $this->expression->toString($prettyPrintOptions);
    }
  }

  public function setScope(Scope $scope): void {
    $this->scope = $scope;
    $this->expression->setScope($scope);
  }

  public function getSubParts(): array {
    return $this->expression->getSubParts();
  }

  public function validate(): Type {
    $this->type = $this->type->validate($this->scope);
    $expressionType = $this->expression->validate();
    if(!$expressionType->canCastTo($this->type)) {
      throw new \BadFunctionCallException('Can\'t cast from '.$expressionType->getIdentifier().' to '.$this->type->getIdentifier());
    }
  }
}
