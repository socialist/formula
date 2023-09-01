<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\src\statement\Statement;
use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\types\Type;
use src\PrettyPrintOptions;

class CodeBlock extends Statement {
  
  /**
   * @var Statement[]
   */
  private array $expressions;
  
  private Type $returnType;
  
  public function __construct(Scope $scope, array $expressions) {
    parent::__construct($scope);
    $this->expressions = $expressions;
  }
  
  public function registerDefines() {
    foreach ($this->expressions as $expression) {
      $expression->registerDefines();
    }
  }
  
  public function validate(FormulaSettings $formulaSettings): Type {
    $parentReturnType = new VoidType();
    foreach ($this->expressions as $expression) {
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
    $locator = null;
    foreach ($this->expressions as $expression) {
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
  
  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $first = true;
    foreach ($this->expressions as $expression) {
      if(!$first) {
        $string .= $prettyPrintOptions->getStatementSeperator();
      }
      $string .= $expression->toString();
      $first = false;
    }
    return $string;
  }
  
  public function getSubExpressions(): array {
    return $this->statements;
  }
}
