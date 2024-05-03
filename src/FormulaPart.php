<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\procedure\StatementReturnInfo;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * Superclass for all parsed components of a formula program
 *
 * @author Timo Lehnertz
 *        
 */
interface FormulaPart {

  /**
   * MUST set the scope of this part.
   * MUST also set the scope for all sub parts
   *
   * Will be called first
   */
  public function setScope(Scope $scope);

  /**
   * will get called second
   */
  public function defineReferences(): void;

  /**
   * Will be called after registerDefines().
   * Must validate this and all sub expressions.
   * Throws exceptions in case of invalidation.
   *
   * will get called thrird
   *
   * @return Type the implied return type of this expression
   */
  public function validate(): Type;

  /**
   * Run this FormulaPart and all neccessary sub Parts
   *
   * @return StatementReturnInfo the container for returned info
   */
  public function run(): Value;

  /**
   *
   * @return array that holds all Parts that make up this one
   */
  public function getSubParts(): array;

  /**
   *
   * @return string Converts this expression back to code using PrettyPrintOptions
   */
  public function toString(?PrettyPrintOptions $prettyPrintOptions): string;
}

