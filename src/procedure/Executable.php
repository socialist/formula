<?php
namespace src\procedure;

use TimoLehnertz\formula\procedure\Scope;

interface Executable {
  
  public function execute(Scope $scope): Value;
}

