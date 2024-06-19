<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

class CodeBlock extends Statement {

  /**
   * @var Statement[]
   */
  private readonly array $statements;

  private readonly bool $singleLine;

  /**
   * @param array<Statement> $statements
   */
  public function __construct(array $statements, bool $singleLine) {
    parent::__construct();
    $this->statements = $statements;
    $this->singleLine = $singleLine;
    if($singleLine && count($statements) !== 1) {
      throw new FormulaBugException('Single line codeblock must contain exactly one statement');
    }
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $scope = $scope->buildChild();
    $statementReturnType = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    foreach($this->statements as $statement) {
      $statementReturnType = $statementReturnType->concatSequential($statement->validate($scope, $allowedReturnType));
    }
    return $statementReturnType;
  }

  public function runStatement(Scope $scope): StatementReturn {
    $scope = $scope->buildChild();
    foreach($this->statements as $statement) {
      $statementReturn = $statement->run($scope);
      if($statementReturn->isTerminating()) {
        return $statementReturn;
      }
    }
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->singleLine) {
      $prettyPrintOptions->indent();
      $str = $prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().$this->statements[0]->toString($prettyPrintOptions).$prettyPrintOptions->newLine;
      $prettyPrintOptions->outdent();
      return $str.$prettyPrintOptions->getIndentStr();
    }
    if(count($this->statements) === 0) {
      return '{}';
    }
    $string = '{';
    $prettyPrintOptions->indent();
    foreach($this->statements as $statement) {
      $string .= $prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().$statement->toString($prettyPrintOptions);
    }
    $prettyPrintOptions->outdent();
    return $string.$prettyPrintOptions->newLine.$prettyPrintOptions->getIndentStr().'}';
  }
}
