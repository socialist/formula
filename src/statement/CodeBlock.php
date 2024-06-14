<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

class CodeBlock implements Statement {

  /**
   * @var Statement[]
   */
  private readonly array $statements;

  private readonly bool $singleLine;

  /**
   * @param Statement[] $statements
   */
  public function __construct(array $statements, bool $singleLine) {
    $this->statements = $statements;
    $this->singleLine = $singleLine;
    if($singleLine && count($statements) !== 1) {
      throw new \UnexpectedValueException('Single line codeblock must contain exactly one statement');
    }
  }

  public function validate(Scope $scope): StatementReturnType {
    $types = [];
    $mayReturn = false;
    $alwaysReturns = false;
    /** @var Statement $expression */
    foreach($this->statements as $statement) {
      $statementReturnType = $statement->validate($scope);
      if($statementReturnType->returnType !== null && ($statementReturnType->alwaysReturns || $statementReturnType->mayReturn)) {
        $types[] = $statementReturnType->returnType;
      }
      if($statementReturnType->alwaysReturns) {
        $mayReturn = true;
        $alwaysReturns = true;
        break;
      }
      if($statementReturnType->mayReturn) {
        $mayReturn = true;
      }
    }
    if(!$alwaysReturns) {

      $types[] = new VoidType();
    }
    $returnType = CompoundType::buildFromTypes($types);
    return new StatementReturnType($returnType, $mayReturn, $alwaysReturns);
  }

  public function run(Scope $scope): StatementReturn {
    $scope = $scope->buildChild();
    $statementReturn = null;
    foreach($this->statements as $statement) {
      $statementReturn = $statement->run($scope);
      if($statementReturn->isTerminating()) {
        return $statementReturn;
      }
    }
    return new StatementReturn(new VoidValue(), false, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->singleLine) {
      $prettyPrintOptions->indent();
      $str = $prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().$this->statements[0]->toString($prettyPrintOptions);
      $prettyPrintOptions->outdent();
      return $str;
    }
    $string = '{';
    $prettyPrintOptions->indent();
    foreach($this->expressions as $expression) {
      $string .= $prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().$expression->toString($prettyPrintOptions);
    }
    $prettyPrintOptions->outdent();
    return $string.$prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().'}';
  }
}
