<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class WhileStatement extends Statement {

  private Expression $condition;

  private CodeBlock $body;

  public function __construct(Expression $condition, CodeBlock $body) {
    parent::__construct();
    $this->condition = $condition;
    $this->body = $body;
  }

  public function validate(Scope $scope): StatementReturnType {
    $this->condition->validate($scope);
    $statementReturnType = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    return $statementReturnType->concatOr($this->body->validate($scope));
  }

  public function run(Scope $scope): StatementReturn {
    while($this->condition->run($scope)->isTruthy()) {
      $return = $this->body->run($scope);
      if($return->returnValue !== null) {
        return new StatementReturn($return->returnValue, false, 0);
      }
      if($return->breakFlag) {
        break;
      }
      $continueCount = $return->continueCount;
      do {
        $this->condition->run($scope);
        $continueCount--;
      } while($continueCount > 0);
    }
    return new StatementReturn(null, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'while ('.$this->condition->toString($prettyPrintOptions).') '.$this->body->toString($prettyPrintOptions);
  }

  public function getCondition(): Expression {
    return $this->condition;
  }

  public function getBody(): CodeBlock {
    return $this->body;
  }
}
