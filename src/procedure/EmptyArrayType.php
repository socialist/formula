<?php
namespace src\procedure;

use TimoLehnertz\formula\types\Type;

class EmptyArrayType extends Type {
  
  public function __construct() {
    parent::__construct(true, '');
  }
  
  public function isAssignableWith(Type $type): bool {
    throw new \BadMethodCallException('Cant test for empty array assignability');
  }
}

