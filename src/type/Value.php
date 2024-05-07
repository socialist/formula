<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\type\Type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
interface Value {

  public function toString(): string;

  public function assign(self $value): void;

  public function getType(): Type;

  public function isTruthy(): bool;
}

