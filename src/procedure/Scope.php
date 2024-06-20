<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\nodes\NodeTreeScope;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\DateIntervalType;
use TimoLehnertz\formula\type\DateIntervalValue;
use TimoLehnertz\formula\type\DateTimeImmutableType;
use TimoLehnertz\formula\type\DateTimeImmutableValue;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\classes\ClassType;
use TimoLehnertz\formula\type\classes\ClassTypeType;
use TimoLehnertz\formula\type\classes\ClassTypeValue;
use TimoLehnertz\formula\type\classes\ConstructorType;
use TimoLehnertz\formula\type\classes\ConstructorValue;
use TimoLehnertz\formula\type\classes\FieldType;
use TimoLehnertz\formula\type\classes\PHPClassInstanceValue;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\type\functions\PHPFunctionBody;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use const false;
use const true;

/**
 * @author Timo Lehnertz
 */
class Scope {

  /**
   * @var array<string, >
   */
  private array $defined = [];

  private ?Scope $parent = null;

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

  public static function reflectionTypeToFormulaType(?ReflectionType $reflectionType): Type {
    if($reflectionType === null) {
      return new MixedType();
    }
    if($reflectionType instanceof ReflectionNamedType) {
      if($reflectionType->isBuiltin()) {
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
          case 'object':
            return new MixedType();
          case 'callable':
            return new FunctionType(new OuterFunctionArgumentListType([new OuterFunctionArgument(new MixedType(), true, false)], true), new MixedType());
        }
      } else {
        return Scope::reflectionClassToType(new \ReflectionClass($reflectionType->getName()));
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

  /**
   * @param ?callable(OuterFunctionArgumentListType): ?Type $specificFunctionReturnType
   */
  public function definePHP(bool $final, string $identifier, mixed $value = null, ?OuterFunctionArgumentListType $argumentType = null, ?callable $specificFunctionReturnType = null): void {
    if($value !== null) {
      $value = Scope::convertPHPVar($value, false, $argumentType, $specificFunctionReturnType);
    }
    $this->define($final, $value[0], $identifier, $value[1]);
  }

  public function define(bool $final, Type $type, string $identifier, ?Value $value = null): void {
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

  /**
   * @param ?callable(OuterFunctionArgumentListType): ?Type $specificFunctionReturnType
   */
  private static function reflectionFunctionToType(ReflectionFunctionAbstract $reflection, ?OuterFunctionArgumentListType $argumentType = null, ?callable $specificFunctionReturnType = null): FunctionType {
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
      $arguments[] = new OuterFunctionArgument(Scope::reflectionTypeToFormulaType($reflectionArgument->getType()), $reflectionArgument->isOptional(), false);
    }
    return new FunctionType($argumentType ?? new OuterFunctionArgumentListType($arguments, $vargs), $returnType, $specificFunctionReturnType);
  }

  private static array $phpClassTypes = [];

  public static function reflectionClassToType(\ReflectionClass $reflection, bool $force = false): ClassType {
    if(!$force && isset(Scope::$phpClassTypes[$reflection->getName()])) {
      return Scope::$phpClassTypes[$reflection->getName()];
    }
    Scope::$phpClassTypes[$reflection->getName()] = new ClassType(null, '--', []); // dummy

    $fieldTypes = [];
    /** @var ReflectionProperty $refelctionProperty */
    foreach($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $refelctionProperty) {
      $fieldTypes[$refelctionProperty->getName()] = new FieldType($refelctionProperty->isReadOnly(), Scope::reflectionTypeToFormulaType($refelctionProperty->getType()));
    }
    /** @var ReflectionMethod $reflectionMethod */
    foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
      if($reflectionMethod->isConstructor()) {
        continue;
      }
      $functionType = Scope::reflectionFunctionToType($reflectionMethod);
      $fieldTypes[$reflectionMethod->getName()] = new FieldType(true, $functionType);
    }
    $parentReflection = $reflection->getParentClass();
    $parentClassType = null;
    if($parentReflection !== false) {
      $parentClassType = Scope::reflectionClassToType($parentReflection);
    }
    $classType = new ClassType($parentClassType, $reflection->getName(), $fieldTypes);
    Scope::$phpClassTypes[$reflection->getName()] = $classType;
    return $classType;
  }

  /**
   * @param ?callable(OuterFunctionArgumentListType): ?Type $specificFunctionReturnType
   * @return array [Type, Value]
   */
  public static function convertPHPVar(mixed $value, bool $onlyValue = false, ?OuterFunctionArgumentListType $argumentType = null, ?callable $specificFunctionReturnType = null): array {
    if($value instanceof Value) {
      return [null,$value];
    } else if($value instanceof \DateTimeImmutable) {
      return [new DateTimeImmutableType(),new DateTimeImmutableValue($value)];
    } else if($value instanceof \DateInterval) {
      return [new DateIntervalType(),new DateIntervalValue($value)];
    } else if(is_string($value) && class_exists($value)) {
      $reflection = new \ReflectionClass($value);
      $classType = Scope::reflectionClassToType($reflection);

      if($reflection->getConstructor() === null) {
        $constructorFunctionType = new FunctionType(new OuterFunctionArgumentListType([], false), new VoidType());
      } else {
        $constructorFunctionType = Scope::reflectionFunctionToType($reflection->getConstructor());
      }

      $constructor = new ConstructorValue(new PHPFunctionBody(function (...$args) use ($reflection) {
        $phpArgs = [];
        foreach($args as $arg) {
          $phpArgs[] = $arg;
        }
        return new PHPClassInstanceValue($reflection->newInstance(...$phpArgs));
      }, false));

      $contructorType = new ConstructorType($constructorFunctionType->arguments, $classType);

      return [new ClassTypeType($contructorType),new ClassTypeValue($constructor)];
    } else if(is_int($value)) {
      return [new IntegerType(),new IntegerValue($value)];
    } else if(is_float($value)) {
      return [new FloatType(),new FloatValue($value)];
    } else if(is_bool($value)) {
      return [new BooleanType(),new BooleanValue($value)];
    } else if(is_string($value)) {
      return [new StringType(),new StringValue($value)];
    } else if($value === null) {
      return [new NullType(),new NullValue()];
    } else if(is_callable($value)) {
      $name = '';
      //     $isFunction = count(explode("::", $name)) === 1;
      //     if($isFunction) {
      //       $reflection = new \ReflectionFunction($callable);
      //     } else {
      is_callable($value, false, $name);
      if(is_array($value)) {
        $className = is_object($value[0]) ? get_class($value[0]) : $value[0];
        $methodName = $value[1];
        $reflection = new ReflectionMethod($className, $methodName);
      } else {
        throw new \InvalidArgumentException('The provided callable is not an array.');
      }
      //     }
      $functionType = Scope::reflectionFunctionToType($reflection, $argumentType, $specificFunctionReturnType);
      $functionBody = new PHPFunctionBody($value, $functionType->generalReturnType instanceof VoidType);
      return [$functionType,new FunctionValue($functionBody)];
    } else if(is_array($value)) {
      $values = [];
      $valueTypes = [];
      $keyTypes = [];
      foreach($value as $key => $element) {
        if(!$onlyValue) {
          $keyRes = Scope::convertPHPVar($key);
          $keyTypes[] = $keyRes[0];
        }
        $elementRes = Scope::convertPHPVar($element);
        $valueTypes[] = $elementRes[0];
        $values[$key] = $elementRes[1];
      }
      if($onlyValue) {
        return [null,new ArrayValue($values)];
      } else {
        return [new ArrayType(CompoundType::buildFromTypes($keyTypes), CompoundType::buildFromTypes($valueTypes)),new ArrayValue($values)];
      }
    } else if(is_object($value)) {
      $reflection = new \ReflectionClass($value);
      $fieldTypes = [];
      //       $fieldValues = [];
      /** @var ReflectionProperty $refelctionProperty */
      foreach($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $refelctionProperty) {
        $fieldTypes[$refelctionProperty->getName()] = new FieldType($refelctionProperty->isReadOnly(), Scope::reflectionTypeToFormulaType($refelctionProperty->getType()));
        //         $fieldValues[$refelctionProperty->getName()] = new FieldValue(Scope::convertPHPVar($refelctionProperty->getValue($value), true)[1]);
      }
      /** @var ReflectionMethod $reflectionMethod */
      foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
        if($reflectionMethod->isConstructor()) {
          continue;
        }
        $functionType = Scope::reflectionFunctionToType($reflectionMethod);
        $fieldTypes[$reflectionMethod->getName()] = new FieldType(true, $functionType);
        //         $fieldValues[$reflectionMethod->getName()] = new FieldValue(new FunctionValue(new PHPFunctionBody([$value,$reflectionMethod->getName()])));
      }
      return [new ClassType(null, $reflection->getName(), $fieldTypes),new PHPClassInstanceValue($value)];
      //       return [new ClassType(null, $reflection->getName(), $fieldTypes),new ClassInstanceValue($fieldValues)];
    }
    throw new FormulaRuntimeException('Unsupported php type');
  }

  public function assignPHP(string $identifier, mixed $value): void {
    $res = Scope::convertPHPVar($value);
    $this->assign($identifier, $res[1]);
  }

  public function assign(string $identifier, Value $value): void {
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
