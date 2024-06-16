<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
interface FormulaParentPart {

  /**
   * @return array<FormulaPart>
   */
  public function getSubParts(): array;
}
