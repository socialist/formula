<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\OperatableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class CompoundType implements Type {

  /**
   * @var Type[]
   */
  private array $subTypes;

  private function __construct(array $subTypes) {
    if(count($subTypes) < 2) {
      throw new \BadFunctionCallException('Compound Type must contain at least two types');
    }
    $this->subTypes = $subTypes;
  }

  /**
   * @param Type $types
   */
  public static function concatManyTypes(array $types): ?Type {
    if(sizeof($types) === 0) {
      return null;
    }
    if(sizeof($types) === 1) {
      return $types[0];
    } else {
      return new CompoundType($types);
    }
  }

  public function simplify(): Type {
    $type = $this->subTypes[0];
    for($i = 1;$i < sizeof($this->subTypes);$i++) {
      $type = static::concatTypes($type, $this->subTypes[$i]);
    }
    return $type;
  }

  public static function concatTypes(Type $a, Type $b): Type {
    if($a->canCastTo($b)) {
      return $a; // A is the more general type here
    }
    if($b->canCastTo($a)) {
      return $b; // B is the more general type here
    }
    //     types are incompatible
    $types = [];
    if($a instanceof CompoundType) {
      $types = array_merge($types, $a->subTypes);
    } else {
      $types = array_merge($types, [$a]);
    }
    if($b instanceof CompoundType) {
      $types = array_merge($types, $b->subTypes);
    } else {
      $types = array_merge($types, [$b]);
    }
    $unique = static::removeDoublicates($types);
    if(sizeof($unique) === 1) {
      return $unique[0];
    } else {
      return new CompoundType($unique);
    }
  }

  /**
   * @param Type[] $types
   * @return Type[]
   */
  private static function removeDoublicates(array $types): array {
    $identifiers = [];
    $uniqueTypes = [];
    // removing doublicates
    foreach($types as $type) {
      $identifier = $type->getIdentifier();
      if(!array_key_exists($identifier, $identifiers)) {
        $uniqueTypes[] = $type;
        $identifiers[$identifier] = true;
      }
    }
    return $uniqueTypes;
  }

  public function assignableBy(Type $type): bool {
    foreach($this->subTypes as $subType) {
      if($subType->assignableBy($type)) {
        return true;
      }
    }
    return false;
  }

  public function getIdentifier(bool $nested = false): string {
    $typeName = "";
    if($nested) {
      $typeName = "(";
    }
    $del = "";
    foreach($this->subTypes as $subType) {
      $typeName .= $del;
      if($subType instanceof CompoundType) {
        $typeName .= $subType->getIdentifier(true);
      } else {
        $typeName .= $subType->getIdentifier();
      }
      $del = "|";
    }
    if($nested) {
      $typeName .= ")";
    }
    return $typeName;
  }

  public function validate(Scope $scope): Type {
    $simplified = $this->simplify();
    if($simplified instanceof CompoundType) {
      $validated = [];
      foreach($simplified->subTypes as $subType) {
        $validated[] = $subType->validate($scope);
      }
      return new CompoundType($validated);
    } else {
      return $simplified->validate($scope);
    }
  }

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type {
    $resultTypes = [];
    foreach($this->subTypes as $subType) {
      $resultTypes[] = $subType->getOperatorResultType($operator, $otherType);
    }
    $type = CompoundType::concatManyTypes($resultTypes);
    if($type instanceof CompoundType) {
      return $type->simplify();
    }
    return $type;
  }
}
