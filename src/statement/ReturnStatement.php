<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\VoidType;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ReturnStatement implements Statement {

  private ?Expression $expression;

  public function __construct(?Expression $expression) {
    $this->expression = $expression;
  }

  public function defineReferences(): void {
    // expressions dont define anything
  }

  public function run(): StatementValue {
    $value = $this->expression->run();
    return new StatementValue($value, null, false, false, true);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->expression === null) {
      return 'return;';
    }
    return 'return '.$this->expression->toString($prettyPrintOptions).';';
  }

  public function setScope(Scope $scope) {
    if($this->expression !== null) {
      $this->expression->setScope($scope);
    }
  }

  public function getSubParts(): array {
    if($this->expression !== null) {
      return $this->expression->getSubParts();
    }
    return [];
  }

  public function validate(): Type {
    if($this->expression !== null) {
      return $this->expression->validate();
    }
    return new VoidType();
  }
}
