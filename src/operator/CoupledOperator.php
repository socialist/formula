<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 *
 *         Represents an operator that itself contains another expression like a[1], a(1) or (int)a
 */
interface CoupledOperator extends FormulaPart {

  public function getCoupledExpression(): Expression;
}
