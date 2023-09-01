<?php
namespace TimoLehnertz\formula\type;


/**
 * 
 * @author Timo Lehnertz
 *
 */
class VoidType extends Type {
  
  public function __construct() {
    parent::__construct(false);
  }

  public function isAssignableWith(Type $type): bool {
    return false;
  }
  
  public function getValue() {
    throw new \Exception("Can't get value of void type");
  }
  
  public function setValue($value) {
    throw new \Exception("Can't set value of void type");
  }
  
  protected function getTypeName(): string {
    return 'void';
  }
  
  public function canCastTo(Type $type): bool {
    
  }
  
  public function castTo(Type $type): Type {
    
  }
}
