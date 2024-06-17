<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class OuterFunctionArgumentListType extends Type {

  /**
   * @var array<OuterFunctionArgument>
   */
  private readonly array $arguments;

  public readonly bool $isVArgs;

  /**
   * @var array<OuterFunctionArgument>
   */
  public function __construct(array $arguments, bool $isVArgs) {
    parent::__construct();
    $this->arguments = $arguments;
    $this->isVArgs = $isVArgs;
    // check that optional parameters are at the end
    $optional = false;
    for($i = 0;$i < count($arguments);$i++) {
      /** @var OuterFunctionArgument $argument */
      $argument = $arguments[$i];
      if($optional && !$argument->optional) {
        throw new FormulaValidationException('Not optional parameter cannot follow optional parameter');
      }
      if($argument->optional) {
        if($isVArgs && $i < count($arguments) - 1) {
          throw new FormulaValidationException('Optional parameter can\'t be followed by VArgs');
        }
        $optional = true;
        if($i === count($arguments) - 1 && $isVArgs && !$argument->optional) {
          throw new FormulaValidationException('VArg parameter must be optional');
        }
      }
    }
    // check that vargs are valid
    if($isVArgs && count($arguments) === 0) {
      throw new \BadMethodCallException('Vargs argument must have at least one argument');
    }
  }

  public function getMaxArgumentCount(): int {
    if($this->isVArgs) {
      return PHP_INT_MAX;
    } else {
      return count($this->arguments);
    }
  }

  public function getMinArgumentCount(): int {
    $count = 0;
    /** @var OuterFunctionArgument $argument */
    foreach($this->arguments as $argument) {
      if($argument->optional) {
        break;
      }
      $count++;
    }
    return $count;
  }

  public function getArgumentType(int $index): ?Type {
    if(isset($this->arguments[$index])) {
      return $this->arguments[$index]->type;
    } else if($this->isVArgs) {
      return $this->arguments[count($this->arguments) - 1]->type;
    }
  }

  protected function typeAssignableBy(Type $type): bool {
    if(!($type instanceof OuterFunctionArgumentListType)) {
      return false;
    }
    // check argument count
    if(count($type->arguments) > $this->getMaxArgumentCount() || count($type->arguments) < $this->getMinArgumentCount()) {
      return false;
    }
    // check invalid types
    for($i = 0;$i < count($type->arguments);$i++) {
      $sourceType = $type->arguments[$i]->type;
      $targetType = $this->getArgumentType($i);
      if(!$targetType->assignableBy($sourceType)) {
        return false;
      }
    }
    return true;
  }

  public function equals(Type $type): bool {
    if(!($type instanceof OuterFunctionArgumentListType)) {
      return false;
    }
    if($this->isVArgs !== $type->isVArgs) {
      return false;
    }
    if($this->arguments === null || $type->arguments === null) {
      return true;
    }
    if(count($this->arguments) !== count($type->arguments)) {
      return false;
    }
    for($i = 0;$i < count($type->arguments);$i++) {
      if(!$type->arguments[$i]->equals($this->arguments[$i])) {
        return false;
      }
    }
    return true;
  }

  public function getIdentifier(bool $isNested = false): string {
    $identifier = '';
    $delimiter = '';
    for($i = 0;$i < count($this->arguments);$i++) {
      $argument = $this->arguments[$i];
      $identifier .= $delimiter;
      if($i === count($this->arguments) - 1 && $this->isVArgs) {
        $identifier .= '...';
      } else if($argument->optional) {
        $identifier .= '?';
      }
      $identifier .= $argument->type->getIdentifier();
      $delimiter = ',';
    }
    return '('.$identifier.')';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    return [];
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return null;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    $args = [];
    /** @var OuterFunctionArgument $argument */
    foreach($this->arguments as $argument) {
      $args[] = $argument->buildNodeInterfaceType();
    }
    return new NodeInterfaceType('OuterFunctionArgumentList', ['arguments' => $args,'isVargs' => $this->isVArgs]);
  }
}
