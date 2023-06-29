<?php
namespace src\procedure;

use TimoLehnertz\formula\types\Type;

class VoidType extends Type {
  
  public function __onstruct() {
    parent::__construct();
  }
}

