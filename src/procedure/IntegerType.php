<?php
namespace src\procedure;

use TimoLehnertz\formula\types\Type;

class IntegerType extends Type {

  public function __construct(bool $isArray) {
    parent::__construct($isArray, 'int');
  }
  
  
  
  public function isAssignableWith(Type $type) {
    
  }
}

