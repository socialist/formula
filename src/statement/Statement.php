<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * A statement is an executable piece of code.
 * E.g. a loop, class, ff statement, assignment or similar
 *
 * @author Timo Lehnertz
 *
 */
interface Statement extends FormulaPart {

  /**
   * Must validate this and all contained Parts.
   * Throws exceptions in case of invalidation.
   *
   * @return Type the implied return type of this expression
   */
  public function validate(Scope $scope): StatementReturnType;

  /**
   * Run this FormulaPart and all neccessary sub Parts
   */
  public function run(Scope $scope): StatementReturn;
}
