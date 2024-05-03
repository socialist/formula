<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\type\Locator;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\VoidType;

class CodeBlock extends Statement {

  /**
   *
   * @var Statement[]
   */
  private array $statements;

  private Type $returnType;

  /**
   *
   * @param Statement[] $statements
   */
  public function __construct(array $statements) {
    parent::__construct($scope);
    $this->statements = $statements;
  }

  public function registerDefines() {
    foreach($this->expressions as $expression) {
      $expression->registerDefines();
    }
  }

  public function validate(FormulaSettings $formulaSettings): Type {
    $parentReturnType = new VoidType();
    foreach($this->expressions as $expression) {
      $returnType = $expression->validate($formulaSettings);
      if($expression instanceof ReturnStatement) {
        if($parentReturnType->isAssignableWith($returnType)) {
          continue;
        } else if($returnType->isAssignableWith($parentReturnType)) {
          $parentReturnType = $returnType;
        } else {
          throw new \Exception('Inconsistent return types found');
        }
      }
    }
    return $parentReturnType;
  }

  public function run(): Locator {
    $this->scope->undefineVariables();
    $locator = null;
    foreach($this->expressions as $expression) {
      $locator = $expression->run();
      if($expression instanceof ReturnStatement) {
        return $locator;
      }
    }
    if($locator !== null) {
      return $locator;
    } else {
      return new Locator(new VoidType(), null);
    }
  }

  public function getSubParts(): array {
    return $this->statements;
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $first = true;
    foreach($this->expressions as $expression) {
      if(!$first) {
        $string .= $prettyPrintOptions->getStatementSeperator();
      }
      $string .= $expression->toString();
      $first = false;
    }
    return $string;
  }
}
