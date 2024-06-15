<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class IfStatement implements Statement {

  private readonly ?Expression $condition;

  private readonly CodeBlock $body;

  private readonly ?IfStatement $else;

  public function __construct(?Expression $condition, CodeBlock $body, ?IfStatement $else) {
    $this->condition = $condition;
    $this->body = $body;
    $this->else = $else;
  }

  public function validate(Scope $scope): StatementReturnType {
    $this->condition?->validate($scope);
    $statementReturnType = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    $statementReturnType = $statementReturnType->concatOr($this->body->validate($scope));
    if($this->else !== null) {
      $statementReturnType = $statementReturnType->concatOr($this->else->validate($scope));
    }
    return $statementReturnType;
  }

  public function run(Scope $scope): StatementReturn {
    if($this->condition === null || $this->condition->run($scope)->isTruthy()) {
      return $this->body->run($scope);
    } else if($this->else !== null) {
      return $this->else->run($scope);
    } else {
      return new StatementReturn(null, false, 0);
    }
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    if($this->condition !== null) {
      $str = 'if ('.$this->condition->toString($prettyPrintOptions).') '.$this->body->toString($prettyPrintOptions);
      if($this->else !== null) {
        $str .= ' else '.$this->else->toString($prettyPrintOptions);
      }
      return $str;
    } else {
      return $this->body->toString($prettyPrintOptions);
    }
  }

  public function getCondition(): Expression {
    return $this->condition;
  }

  public function getBody(): CodeBlock {
    return $this->body;
  }
}
