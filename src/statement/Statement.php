<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaStatementException;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\Type;

/**
 * A statement is an executable piece of code.
 * E.g. a loop, class, ff statement, assignment or similar
 *
 * @author Timo Lehnertz
 */
abstract class Statement implements FormulaPart {

  public ?Token $firstToken = null;

  public function __construct() {}

  public function validate(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    FormulaStatementException::setCurrentStatement($this);
    return $this->validateStatement($scope, $allowedReturnType);
  }

  /**
   * MUST validate this and all contained Parts.
   * MUST be called EXACTLY one time
   *
   * @return StatementReturnType the implied return type of this expression
   * @throws FormulaValidationException
   */
  public abstract function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType;

  public function run(Scope $scope): StatementReturn {
    FormulaStatementException::setCurrentStatement($this);
    return $this->runStatement($scope);
  }

  public abstract function runStatement(Scope $scope): StatementReturn;

  public function setFirstToken(Token $firstToken): void {
    $this->firstToken = $firstToken;
  }
}
