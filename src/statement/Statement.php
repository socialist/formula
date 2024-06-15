<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\procedure\Scope;

/**
 * A statement is an executable piece of code.
 * E.g. a loop, class, ff statement, assignment or similar
 *
 * @author Timo Lehnertz
 *
 */
interface Statement extends FormulaPart {

  /**
   * MUST validate this and all contained Parts.
   * MUST be called EXACTLY one time
   *
   * @return StatementReturnType the implied return type of this expression
   * @throws FormulaValidationException
   */
  public function validate(Scope $scope): StatementReturnType;

  /**
   * Run this Statement
   */
  public function run(Scope $scope): StatementReturn;

  public function buildNode(Scope $scope): array;
}
