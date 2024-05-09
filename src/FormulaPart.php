<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * Superclass for all parsed components of a formula program
 *
 * @author Timo Lehnertz
 *
 */
interface FormulaPart {

  /**
   * Will be called after registerDefines().
   * Must validate this and all contained Parts.
   * Throws exceptions in case of invalidation.
   *
   * will get called thrird
   *
   * @return Type the implied return type of this expression
   */
  public function validate(Scope $scope): Type;

  /**
   * @return FormulaPart[] that holds all Parts that make up this one
   */
  public function getSubParts(): array;

  /**
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public function toString(PrettyPrintOptions $prettyPrintOptions): string;
}

