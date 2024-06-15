<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign if its returntype is non void.
 * Expressions don't define anything in their scopes
 *
 * @author Timo Lehnertz
 */
abstract class Expression extends FormulaPart {

  /**
   * Must validate this and all contained Parts.
   * Can be called multiple times
   *
   * @return Type the implied return type of this expression
   * @throws FormulaValidationException
   */
  public abstract function validate(Scope $scope): Type;

  public abstract function run(Scope $scope): Value;

  public abstract function buildNode(Scope $scope): array;
}
