<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * @author Timo Lehnertz
 */
interface Value {

  public function toString(): string;

  public function assign(self $value): void;

  public function getType(): Type;

  public function isTruthy(): bool;

  public function copy(): Value;
}

