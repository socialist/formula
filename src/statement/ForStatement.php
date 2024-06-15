<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class ForStatement extends Statement {

  private ?VariableDeclarationStatement $declarationStatement;

  private ?Expression $condition;

  private ?Expression $incrementExpression;

  private CodeBlock $body;

  public function __construct(?VariableDeclarationStatement $declarationStatement, ?Expression $condition, ?Expression $incrementExpression, CodeBlock $body) {
    parent::__construct();
    $this->declarationStatement = $declarationStatement;
    $this->condition = $condition;
    $this->incrementExpression = $incrementExpression;
    $this->body = $body;
  }

  public function validate(Scope $scope): StatementReturnType {
    $scope = $scope->buildChild();
    $this->declarationStatement?->validate($scope);
    $this->condition?->validate($scope);
    $this->incrementExpression?->validate($scope);
    $statementReturnType = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    return $statementReturnType->concatOr($this->body->validate($scope));
  }

  public function run(Scope $scope): StatementReturn {
    $scope = $scope->buildChild();

    $this->declarationStatement?->run($scope);
    $this->incrementExpression?->validate($scope);

    while($this->condition === null || $this->condition->run($scope)->isTruthy()) {
      $return = $this->body->run($scope);
      if($return->returnValue !== null) {
        return new StatementReturn($return->returnValue, false, 0);
      }
      if($return->breakFlag) {
        break;
      }
      $continueCount = $return->continueCount;
      do {
        $this->incrementExpression?->run($scope);
        $continueCount--;
      } while($continueCount > 0);
    }
    return new StatementReturn(null, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'for ('.($this->declarationStatement?->toString($prettyPrintOptions) ?? '; ').($this->condition?->toString($prettyPrintOptions) ?? '').';'.($this->incrementExpression?->toString($prettyPrintOptions) ?? '').') '.$this->body->toString($prettyPrintOptions);
  }

  public function getCondition(): Expression {
    return $this->condition;
  }

  public function getBody(): CodeBlock {
    return $this->body;
  }
}
