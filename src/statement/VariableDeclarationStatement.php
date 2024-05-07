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
class VariableDeclarationStatement implements Statement {

  private Type $type;

  private readonly string $identifier;

  private readonly ?Expression $initilizer;

  private Scope $scope;

  public function __construct(Type $type, string $identifier, ?Expression $initilizer) {
    $this->type = $type;
    $this->identifier = $identifier;
    $this->initilizer = $initilizer;
  }

  public function defineReferences(): void {
    // nothing to define
  }

  public function run(): StatementValue {}

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->initilizer === null) {
      return $this->type->getIdentifier().' '.$this->identifier.';';
    } else {
      return $this->type->getIdentifier().' '.$this->identifier.' = '.$this->initilizer->toString($prettyPrintOptions).';';
    }
  }

  public function setScope(Scope $scope) {
    $this->scope = $scope;
    $this->initilizer->setScope($scope);
  }

  public function getSubParts(): array {
    if($this->initilizer !== null) {
      return $this->initilizer->getSubParts();
    }
    return [];
  }

  public function validate(): Type {
    $this->type = $this->type->validate($this->scope);
    if($this->initilizer !== null) {
      $initilizerType = $this->initilizer->validate();
      if(!$initilizerType->canCastTo($this->type)) {
        throw new \BadFunctionCallException('cant initialize variable \"'.$this->identifier.'\" of type '.$this->type->getIdentifier().' with type '.$initilizerType->getIdentifier());
      }
    }
  }
}
