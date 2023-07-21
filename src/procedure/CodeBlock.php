<?php
namespace TimoLehnertz\formula\procedure;

use src\procedure\Statement;
use src\procedure\VoidType;

class CodeBlock implements Statement {
  
  private Scope $scope;
  
  /**
   * @var Statement[]
   */
  private array $statements;
  
  public function __construct(array $statements) {
    $this->statements = $statements;
  }
  
  public function validate(Scope $scope, bool $throwOnMissingDependencies): void {
    $this->scope = $scope->getChild();
    foreach ($this->statements as $statement) {
      $statement->validate($this->scope, $throwOnMissingDependencies);
    }
  }
  
  public function run(): ReturnValue {
    $value = new ReturnValue(new Value(new VoidType(), null), null);
    foreach ($this->statements as $statement) {
      $value = $statement->run();
    }
    return $value;
  }
  
  public function getScope(): Scope {
    return $this->scope;
  }

  public function toString(): string {
    $string = '';
    foreach ($this->statements as $statement) {
      $string .= $statement->toString().PHP_EOL;
    }
    return $string;
  }

}

