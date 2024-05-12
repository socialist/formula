<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
interface Operator {

  public function validateOperation(?Type $leftType, ?Type $rigthType): Type;

  public function operate(?Value $leftValue, ?Value $rightValue): Value;
}
