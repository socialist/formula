<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

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

  public function define(string $identifier, Type $type): void {
    if(isset($this->defined[$identifier])) {
      throw new FormulaRuntimeException('Can\'t redefine '.$identifier);
    }
    $this->defined[$identifier] = new DefinedValue($type);
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

  private static function valueByPHPVar(mixed $value): Value {
    if(is_int($value)) {
      return new IntegerValue($value);
    } else if(is_float($value)) {
      return new FloatValue($value);
    } else if(is_bool($value)) {
      return new BooleanValue($value);
    } else if(is_string($value)) {
      return new StringValue($value);
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
}
