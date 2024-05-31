<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class CompoundType implements Type {

  private readonly array $types;

  private function __construct(array $types) {
    $this->types = $types;
  }

  public static function buildFromTypes(array $types): Type {
    if(count($types) === 0) {
      throw new \UnexpectedValueException('types must contain at least one type');
    }
    $uniqueTypes = [];
    // eliminate clones
    foreach($types as $type) {
      $found = false;
      foreach($uniqueTypes as $uniqueType) {
        if($uniqueType->equals($type)) {
          $found = true;
          break;
        }
      }
      if(!$found) {
        $uniqueTypes[] = $type;
      }
    }
    if(count($uniqueTypes) === 1) {
      return $uniqueTypes[0];
    } else {
      return new CompoundType($uniqueTypes);
    }
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    $resultTypes = [];
    foreach($this->types as $type) {
      $resultTypes[] = $type->getOperatorResultType($operator, $otherType);
    }
    return CompoundType::buildFromTypes($resultTypes);
  }

  public function getIdentifier(bool $nested = false): string {
    $identifier = '';
    $delimiter = '';
    foreach($this->types as $type) {
      $identifier .= $delimiter.$type->getIdentifier(true);
      $delimiter = '|';
    }
    if($nested) {
      return '('.$identifier.')';
    } else {
      return $identifier;
    }
  }

  public function equals(Type $type): bool {
    if($type instanceof CompoundType) {
      if(count($type->types) !== count($this->types)) {
        return false;
      }
      foreach($type->types as $otherType) {
        $found = false;
        foreach($this->types as $ownType) {
          if($ownType->equals($otherType)) {
            $found = true;
            break;
          }
        }
        if(!$found) {
          return false;
        }
      }
      return true;
    } else {
      return false;
    }
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    throw new \BadFunctionCallException("Not yet implemented");
    $operandTypes = $this->types[0]->getCompatibleOperands($operator);
    for($i = 1;$i < count($this->types);$i++) {
      $type = $this->types[$i];
      foreach($type->getCompatibleOperands() as $otherOperandTypes) {
      /**
       * @todo
       */
      }
    }
    return $operandTypes;
  }

  public function buildNode(): array {
    $types = [];
    foreach($this->types as $type) {
      $type[] = $type->buildNode();
    }
    return ['type' => 'CompoundType','types' => $types];
  }
}

