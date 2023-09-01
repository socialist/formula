<?php
namespace TimoLehnertz\formula\type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class IntegerType extends Type {

  public function __construct(bool $isArray) {
    parent::__construct($isArray, 'int');
  }
  
  
  
  public function isAssignableWith(Type $type) {
    
  }
}

