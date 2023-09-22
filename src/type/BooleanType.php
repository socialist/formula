<?php
namespace TimoLehnertz\formula\type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class BooleanType extends Type {
  
  public function __construct(bool $isArray) {
    parent::__construct($isArray);
  }

  public function isAssignableWith(Type $type) {
    return true;
  }
  
  public function canCastTo(Type $type): bool {
    
  }
  
  public function castTo(Type $type): Type {
    
  }
  
  protected function getTypeName(): string {
    
  }
}

