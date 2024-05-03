<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\StatementReturnInfo;

/**
 * A statement is an executable piece of code.
 * E.g. a loop, class, ff statement, assignment or similar
 *
 * @author Timo Lehnertz
 *        
 */
interface Statement extends FormulaPart {

  /**
   * Run this FormulaPart and all neccessary sub Parts
   */
  public function run(): StatementReturnInfo;
}

