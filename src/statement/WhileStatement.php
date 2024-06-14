<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class WhileStatement implements Statement {

  private Expression $condition;

  private CodeBlock $body;

  public function __construct(Expression $condition, CodeBlock $body) {
    $this->condition = $condition;
    $this->body = $body;
  }

  public function validate(Scope $scope): StatementReturnType {
    $this->condition->validate($scope);
    $statementReturnType = $this->body->validate($scope);
    return new StatementReturnType($statementReturnType->returnType, $statementReturnType->mayReturn, false);
  }

  public function run(Scope $scope): StatementReturn {
    while($this->condition->run($scope)->isTruthy()) {
      $return = $this->body->run($scope);
      if($return->returnFlag) {
        return new StatementReturn($return->returnValue, true, false, 0);
      }
      if($return->breakFlag) {
        break;
      }
      $continueCount = $return->continueCount;
      while($continueCount > 1) {
        $this->condition->run($scope);
      }
    }
    return new StatementReturn(new VoidValue(), false, false, 0);
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
