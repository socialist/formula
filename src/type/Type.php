<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * Must be immutable
 * @author Timo Lehnertz
 */
interface Type extends OperatorMeta {

  /**
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public function getIdentifier(bool $nested = false): string;

  public function equals(Type $type): bool;

  public function assignableBy(Type $type): bool;

  public function buildNode(): array;
}
