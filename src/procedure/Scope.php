<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\nodes\NodeTreeScope;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\type\functions\PHPFunctionBody;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;

/**
 * @author Timo Lehnertz
 */
class Scope {

  /**
   * @var array<string, >
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
    throw new \BadMethodCallException('PHP type '.$reflectionType.' is not supported');
  }

  public function definePHPFunction(string $identifier, callable $callable): void {
    $name = '';
    is_callable($callable, false, $name);
    //     $isFunction = count(explode("::", $name)) === 1;
    //     if($isFunction) {
    //       $reflection = new \ReflectionFunction($callable);
    //     } else {
    if(is_array($callable)) {
      $className = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
      $methodName = $callable[1];
      $reflection = new ReflectionMethod($className, $methodName);
    } else {
      throw new \InvalidArgumentException('The provided callable is not an array.');
    }
    //     }
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
      $arguments[] = new OuterFunctionArgument(Scope::reflectionTypeToFormulaType($reflectionArgumentType), $reflectionArgument->isOptional());
    }
    $functionType = new FunctionType(new OuterFunctionArgumentListType($arguments, $vargs), $returnType);
    $functionBody = new PHPFunctionBody($callable, $returnType);
    $this->define(true, $functionType, $identifier, new FunctionValue($functionBody, $this));
  }

  public function define(bool $final, Type $type, string $identifier, mixed $value = null): void {
    if($value !== null && !($value instanceof Value)) {
      $value = Scope::valueByPHPVar($value);
    }
    if(isset($this->defined[$identifier])) {
      throw new FormulaRuntimeException('Can\'t redefine '.$identifier);
    }
    $this->defined[$identifier] = new DefinedValue($final, $type, $value);
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
    } else if(is_array($value)) {
      $values = [];
      foreach($value as $element) {
        $values[] = Scope::valueByPHPVar($element);
      }
      return new ArrayValue($values);
    }
    throw new FormulaRuntimeException('Unsupported php type');
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

  public function setParent(Scope $parent): void {
    $this->parent = $parent;
  }

  public function toNodeTreeScope(): NodeTreeScope {
    $definedValues = [];
    /** @var DefinedValue $definedValue */
    foreach($this->defined as $identifier => $definedValue) {
      $definedValues[$identifier] = $definedValue->type->buildNodeInterfaceType();
    }
    return new NodeTreeScope($this->parent?->toNodeTreeScope() ?? null, $definedValues);
  }
}
