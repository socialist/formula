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
class DoWhileStatement extends Statement {

  private CodeBlock $body;

  private Expression $condition;

  public function __construct(CodeBlock $body, Expression $condition) {
    parent::__construct();
    $this->body = $body;
    $this->condition = $condition;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $this->condition->validate($scope);
    return $this->body->validate($scope, $allowedReturnType);
  }

  public function runStatement(Scope $scope): StatementReturn {
    do {
      $return = $this->body->run($scope);
      if($return->returnValue !== null) {
        return new StatementReturn($return->returnValue, false, false);
      }
      if($return->breakFlag) {
        break;
      }
    } while($this->condition->run($scope)->isTruthy());
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return 'do '.$this->body->toString($prettyPrintOptions).' while('.$this->condition->toString($prettyPrintOptions).');';
  }
}
