<?php
namespace TimoLehnertz\formula\type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class BooleanType extends Type {
  
  public function __construct(bool $isArray) {
    parent::__construct($isArray, 'bool');
  }

  public function isAssignableWith(Type $type) {
    
  }
}

