<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class ArgumentListType implements Type {

  /**
   * @var array<FunctionArgument>
   */
  private readonly array $arguments;

  public readonly bool $isVArgs;

  /**
   * @var array<FunctionArgument>
   */
  public function __construct(array $arguments, bool $isVArgs) {
    $this->arguments = $arguments;
    $this->isVArgs = $isVArgs;
    // check that optional parameters are at the end
    $optional = false;
    for($i = 0;$i < count($arguments);$i++) {
      /** @var FunctionArgument $argument */
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
    /** @var FunctionArgument $argument */
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

  public function assignableBy(Type $type): bool {
    if(!($type instanceof ArgumentListType)) {
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
    if(!($type instanceof ArgumentListType)) {
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

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new ArgumentListValue([], $this))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new ArgumentListValue([], $this))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    $argumentNodes = [];
    foreach($this->arguments as $argument) {
      $argumentNodes[] = $argument->buildNode();
    }
    return ['type' => 'ArgumentListType','arguments' => $argumentNodes,'isVargs' => $this->isVArgs];
  }
}
