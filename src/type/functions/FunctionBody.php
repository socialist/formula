<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
interface FunctionBody {

  public function call(OuterFunctionArgumentListValue $args): Value;
}
