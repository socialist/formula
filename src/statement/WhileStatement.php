<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

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

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $this->condition->validate($scope);
    $statementReturnType = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    return $statementReturnType->concatOr($this->body->validate($scope, $allowedReturnType));
  }

  public function runStatement(Scope $scope): StatementReturn {
    while($this->condition->run($scope)->isTruthy()) {
      $return = $this->body->run($scope);
      if($return->returnValue !== null) {
        return new StatementReturn($return->returnValue, false, false);
      }
      if($return->breakFlag) {
        break;
      }
    }
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'while ('.$this->condition->toString($prettyPrintOptions).') '.$this->body->toString($prettyPrintOptions);
  }
}
