<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class FunctionType implements Type {

  public readonly ArgumentListType $arguments;

  public readonly Type $returnType;

  public function __construct(ArgumentListType $arguments, Type $returnType) {
    $this->arguments = $arguments;
    $this->returnType = $returnType;
  }

  public function equals(Type $type): bool {
    if($type instanceof FunctionType) {
      return $this->arguments->equals($type->arguments) && $this->returnType->equals($type->returnType);
    } else {
      return false;
    }
  }

  public function getIdentifier(bool $nested = false): string {
    $str = '';
    $del = '';
    foreach($this->arguments as $argument) {
      $str .= $del.$argument->type->getIdentifier();
      $del = ',';
    }
    return '('.$str.') => '.$this->returnType->getIdentifier();
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new FunctionValue([$this,'getIdentifier'], $this, new Scope()))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new FunctionValue([$this,'getIdentifier'], $this, new Scope()))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    return ['type' => 'FunctionType'];
  }
}
