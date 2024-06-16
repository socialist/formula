<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\IntegerType;

/**
 * @author Timo Lehnertz
 *
 *         Represents a function argument as seen from inside a function
 */
class InnerFunctionArgumentList {

  /**
   * @var array<InnerFunctionArgument>
   */
  private readonly array $arguments;

  private readonly ?InnerVargFunctionArgument $varg;

  private readonly int $minArgCount;

  /**
   * @param array<InnerFunctionArgument> $arguments
   */
  public function __construct(array $arguments, ?InnerVargFunctionArgument $varg) {
    $this->arguments = $arguments;
    $this->varg = $varg;
    $minArgCount = 0;
    /** @var InnerFunctionArgument $arg */
    foreach($this->arguments as $arg) {
      if(!$arg->isOptional()) {
        $minArgCount++;
      }
    }
    $this->minArgCount = $minArgCount;
  }

  public function populateScope(Scope $scope, OuterFunctionArgumentListValue $args): void {
    if(count($args->getValues()) < $this->minArgCount) {
      throw new FormulaBugException('Not enough arguments');
    }
    $i = 0;
    $count = min(count($this->arguments), count($args->getValues()));
    for(;$i < $count;$i++) {
      $value = $args->getValues()[$i];
      /** @var InnerFunctionArgument $innerArgument */
      $innerArgument = $this->arguments[$i];
      $scope->define($innerArgument->final, $innerArgument->type, $innerArgument->name, $value);
    }
    if($this->varg !== null) {
      $values = [];
      for(;$i < count($args->getValues());$i++) {
        $values[] = $args->getValues()[$i];
      }
      $arrayType = new ArrayType(new IntegerType(), $this->varg->type, $this->varg->type->isFinal());
      $arrayValue = new ArrayValue($values, $arrayType);
      $scope->define($this->varg->final, $arrayType, $this->varg->name, $arrayValue);
    }
  }

  public function populateScopeDefinesOnly(Scope $scope): void {
    /**  @var InnerFunctionArgument $argument */
    foreach($this->arguments as $argument) {
      $scope->define($argument->final, $argument->type, $argument->name);
    }
    if($this->varg !== null) {
      $scope->define($this->varg->final, new ArrayType(new IntegerType(), $this->varg->type), $this->varg->name);
    }
  }

  public function toOuterType(): OuterFunctionArgumentListType {
    $args = [];
    /** @var InnerFunctionArgument $argument */
    foreach($this->arguments as $argument) {
      $args[] = new OuterFunctionArgument($argument->type, $argument->isOptional());
    }
    if($this->varg !== null) {
      $args[] = new OuterFunctionArgument($this->varg->type, true);
    }
    return new OuterFunctionArgumentListType($args, $this->varg !== null);
  }
}
