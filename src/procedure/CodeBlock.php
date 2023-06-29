<?php
namespace TimoLehnertz\formula\procedure;

use src\procedure\Executable;
use src\procedure\Value;
use src\procedure\VoidType;

class CodeBlock implements Executable {
  
  private Scope $scope;
  
  /**
   * @var Executable[]
   */
  private array $code;
  
  public function __construct() {
    $this->scope = new Scope();
  }
  
  public function execute(): Value {
    $value = new Value(new VoidType(), null);
    foreach ($this->code as $executable) {
      $value = $executable->execute();
    }
    return $value;
  }
  
  public function getScope(): Scope {
    return $this->scope;
  }
}

