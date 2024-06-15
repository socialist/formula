<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class FunctionType extends Type {

  public readonly ArgumentListType $arguments;

  public readonly Type $returnType;

  public function __construct(ArgumentListType $arguments, Type $returnType) {
    parent::__construct();
    $this->arguments = $arguments;
    $this->returnType = $returnType;
  }

  public function assignableBy(Type $type): bool {
    return $this->equals($type);
  }

  public function equals(Type $type): bool {
    if($type instanceof FunctionType) {
      return $this->arguments->equals($type->arguments) && $this->returnType->equals($type->returnType);
    } else {
      return false;
    }
  }

  public function getIdentifier(bool $nested = false): string {
    return $this->arguments->getIdentifier().': '.$this->returnType->getIdentifier();
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new FunctionValue([$this,'getIdentifier'], $this, new Scope()))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new FunctionValue([$this,'getIdentifier'], $this, new Scope()))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'FunctionType','signature' => $this->getIdentifier()];
  }
}
