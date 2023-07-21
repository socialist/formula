<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\types\Type;

class BooleanType extends Type {
  
  public function __construct(bool $isArray) {
    parent::__construct($isArray, 'bool');
  }

  public function isAssignableWith(Type $type) {
    
  }
}

