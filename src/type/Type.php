<?php
namespace TimoLehnertz\formula\type;

/**
 * Read only class for dealing with types
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Type {
  
  private bool $isArray;
  private string $name;
  
  public function __construct(bool $isArray) {
    $this->isArray = $isArray;
  }
  
  public abstract function isAssignableWith(Type $type): bool;
  
  public function toString(): string {
    if($this->isArray) {
      return $this->getTypeName().'[]';
    } else {
      return $this->getTypeName();
    }
  }
  
  protected abstract function getTypeName(): string;
  
  public abstract function canCastTo(Type $type): bool;
  
  public abstract function castTo(Type $type): Type;
}
