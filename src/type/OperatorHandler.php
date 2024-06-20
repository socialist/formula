<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
interface OperatorHandler {

  /**
   * Must have been validated previously
   * @param Value $other the optional operand
   * @return Value The resulting value
   */
  public function operate(ImplementableOperator $operator, ?Value $other): Value;
}
