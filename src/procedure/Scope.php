<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\type\ArgumentListType;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\FunctionArgument;
use TimoLehnertz\formula\type\FunctionType;
use TimoLehnertz\formula\type\FunctionValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use TimoLehnertz\formula\type\NullValue;

/**
 * @author Timo Lehnertz
 */
class Scope {

  /**
   * @var array<string, DefinedValue>
   */
  private array $defined = [];

  private ?Scope $parent = null;

  public function __construct() {}

  public function buildChild(): Scope {
    $child = new Scope();
    $child->parent = $this;
    return $child;
  }

  public function isDefined(string $identifier): bool {
    if(isset($this->defined[$identifier])) {
      return true;
    } else {
      return $this->parent?->isDefined($identifier) ?? false;
    }
  }

  public static function reflectionTypeToFormulaType(ReflectionType $reflectionType): Type {
    if($reflectionType instanceof ReflectionNamedType) {
      switch($reflectionType->getName()) {
        case 'string':
          return new StringType();
        case 'int':
          return new IntegerType();
        case 'float':
          return new FloatType();
        case 'bool':
          return new BooleanType();
        case 'array':
          return new ArrayType(new MixedType(), new MixedType());
        case 'mixed':
          return new MixedType();
        case 'void':
          return new VoidType();
      }
    } else if($reflectionType instanceof \ReflectionUnionType) {
      $types = [];
      foreach($reflectionType->getTypes() as $type) {
        $types[] = self::reflectionTypeToFormulaType($type);
      }
      return CompoundType::buildFromTypes($types);
    }
    throw new FormulaValidationException('PHP type '.$reflectionType.' is not supported');
  }

  public function definePHPFunction(string $identifier, callable $callable): void {
    $name = '';
    is_callable($callable, false, $name);
    $isFunction = count(explode("::", $name)) === 1;
    if($isFunction) {
      $reflection = new \ReflectionFunction($callable);
    } else {
      if(is_array($callable)) {
        $className = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
        $methodName = $callable[1];
        $reflection = new ReflectionMethod($className, $methodName);
      } else {
        throw new \InvalidArgumentException('The provided callable is not an array.');
      }
    }
    /** @var \ReflectionFunctionAbstract $reflection */
    $reflectionReturnType = $reflection->getReturnType();
    if($reflectionReturnType !== null) {
      $returnType = Scope::reflectionTypeToFormulaType($reflectionReturnType);
    } else {
      $returnType = new VoidType();
    }
    $arguments = [];
    $reflectionArguments = $reflection->getParameters();
    $vargs = false;
    /**  @var ReflectionParameter  $reflectionArgument */
    foreach($reflectionArguments as $reflectionArgument) {
      if($reflectionArgument->isVariadic()) {
        $vargs = true;
      }
      $reflectionArgumentType = $reflectionArgument->getType();
      if($reflectionArgumentType === null) {
        throw new FormulaValidationException('Parameter '.$reflectionArgument->name.' has no php type. Only typed parameters are supported');
      }
      $arguments[] = new FunctionArgument(Scope::reflectionTypeToFormulaType($reflectionArgumentType), $reflectionArgument->isOptional());
    }
    $functionType = new FunctionType(new ArgumentListType($arguments, $vargs), $returnType);
    $this->define($identifier, $functionType, new FunctionValue($callable, $functionType, $this));
  }

  public function define(string $identifier, Type $type, ?Value $value = null): void {
    if(isset($this->defined[$identifier])) {
      throw new FormulaRuntimeException('Can\'t redefine '.$identifier);
    }
    $this->defined[$identifier] = new DefinedValue($type);
    if($value !== null) {
      $this->assign($identifier, $value);
    }
  }

  public function get(string $identifier): Value {
    if(isset($this->defined[$identifier])) {
      return $this->defined[$identifier]->get();
    } else if($this->parent !== null) {
      return $this->parent->get($identifier);
    } else {
      throw new FormulaRuntimeException($identifier.' is not defined');
    }
  }

  public function getType(string $identifier): Type {
    if(isset($this->defined[$identifier])) {
      return $this->defined[$identifier]->type;
    } else if($this->parent !== null) {
      return $this->parent->getType($identifier);
    } else {
      throw new FormulaRuntimeException($identifier.' is not defined');
    }
  }

  public function assignableBy(string $identifier, Type $type): bool {
    if(isset($this->defined[$identifier])) {
      return $this->defined[$identifier]->type->equals($type);
    } else if($this->parent !== null) {
      return $this->parent->assignableBy($identifier, $type);
    } else {
      throw new FormulaRuntimeException($identifier.' is not defined');
    }
  }

  public static function valueByPHPVar(mixed $value): Value {
    if(is_int($value)) {
      return new IntegerValue($value);
    } else if(is_float($value)) {
      return new FloatValue($value);
    } else if(is_bool($value)) {
      return new BooleanValue($value);
    } else if(is_string($value)) {
      return new StringValue($value);
    } else if($value === null) {
      return new NullValue();
    }
    throw new FormulaRuntimeException($value.' has no supported php type');
  }

  public function assign(string $identifier, mixed $value): void {
    if(!($value instanceof Value)) {
      $value = Scope::valueByPHPVar($value);
    }
    if(isset($this->defined[$identifier])) {
      $this->defined[$identifier]->assign($value);
    } else if($this->parent !== null) {
      $this->parent->assign($identifier, $value);
    } else {
      throw new FormulaRuntimeException($identifier.' is not defined');
    }
  }

  public function copy(): Scope {
    $copy = new Scope();
    $copy->parent = $this->parent;
    foreach($this->defined as $identifier => $defined) {
      $copy->defined[$identifier] = $defined->copy();
    }
    return $copy;
  }

  public function setParent(Scope $parent): void {
    $this->parent = $parent;
  }
}
