<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\nodes\NodeInterfaceType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\MemberAccsessType;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class ClassType extends Type {

  protected readonly ?ClassType $parentType;

  protected readonly string $identifier;

  /**
   * @var array<string, FieldType>
   */
  protected readonly array $fields;

  /**
   * @param array<string, FieldType> $fields
   */
  public function __construct(?ClassType $parentType, string $identifier, array $fields) {
    parent::__construct();
    $this->parentType = $parentType;
    $this->identifier = $identifier;
    if($this->parentType !== null) {
      $this->fields = array_merge($fields, $this->parentType->fields);
    } else {
      $this->fields = $fields;
    }
  }

  protected function typeAssignableBy(Type $type): bool {
    if(!($type instanceof ClassType)) {
      return false;
    }
    return $this->equals($type) || $type->extends($this);
  }

  public function extends(ClassType $classType): bool {
    if($this->parentType === null) {
      return false;
    }
    if($this->parentType->equals($classType)) {
      return true;
    }
    return $this->parentType->extends($classType);
  }

  public function equals(Type $type): bool {
    if(!($type instanceof ClassType)) {
      return false;
    }
    if($this->identifier !== $type->identifier) {
      return false;
    }
    if(count($this->fields) !== count($type->fields)) {
      return false;
    }
    /** @var FieldType $field */
    foreach($this->fields as $identifier => $field) {
      if(!isset($type->fields[$identifier])) {
        return false;
      }
      if(!$field->equals($type->fields[$identifier])) {
        return false;
      }
    }
    return true;
  }

  public function getIdentifier(bool $isNested = false): string {
    return 'classType('.$this->identifier.')';
  }

  protected function getTypeCompatibleOperands(ImplementableOperator $operator): array {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        $compatible = [];
        foreach(array_keys($this->fields) as $identifier) {
          $compatible[] = new MemberAccsessType($identifier);
        }
        return $compatible;
      default:
        return [];
    }
  }

  protected function getTypeOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        if(!($otherType instanceof MemberAccsessType)) {
          break;
        }
        if(!isset($this->fields[$otherType->getMemberIdentifier()])) {
          break;
        }
        return $this->fields[$otherType->getMemberIdentifier()]->type;
    }
    return null;
  }

  public function buildNodeInterfaceType(): NodeInterfaceType {
    return new NodeInterfaceType('ClassType');
  }
}
