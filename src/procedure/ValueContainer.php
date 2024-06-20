<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
interface ValueContainer {

  public function assign(Value $value): void;
}
